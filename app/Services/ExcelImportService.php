<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class ExcelImportService
{
    /**
     * @return array<int, array{question_text: string, option_a: string, option_b: string, option_c: string, option_d: string, correct_option: string}>
     */
    public function importQuestions(UploadedFile $file): array
    {
        $extension = Str::lower($file->getClientOriginalExtension());

        $rows = match ($extension) {
            'csv', 'txt' => $this->readCsv($file->getRealPath()),
            'xlsx' => $this->readXlsx($file->getRealPath()),
            default => throw ValidationException::withMessages([
                'import_file' => 'File import phải có định dạng CSV hoặc XLSX.',
            ]),
        };

        return $this->normalizeRows($rows);
    }

    public function sampleCsvContent(): string
    {
        return "\xEF\xBB\xBF".implode("\n", [
            'question_text,option_a,option_b,option_c,option_d,correct_option',
            '"Laravel được viết bằng ngôn ngữ nào?","Java","Python","PHP","JavaScript","C"',
            '"Lệnh chạy migration trong Laravel là gì?","php artisan migrate","php artisan serve","composer install","npm run dev","A"',
        ]);
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function readCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'import_file' => 'Không thể đọc file import.',
            ]);
        }

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = array_map(fn ($value) => trim((string) $value), $row);
        }

        fclose($handle);

        return $rows;
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw ValidationException::withMessages([
                'import_file' => 'Máy chủ chưa bật ZipArchive để đọc file XLSX. Vui lòng dùng CSV.',
            ]);
        }

        if (! function_exists('simplexml_load_string')) {
            throw ValidationException::withMessages([
                'import_file' => 'Máy chủ chưa bật SimpleXML để đọc file XLSX. Vui lòng dùng CSV.',
            ]);
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages([
                'import_file' => 'File XLSX không hợp lệ hoặc không thể mở.',
            ]);
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw ValidationException::withMessages([
                'import_file' => 'File XLSX phải có sheet đầu tiên chứa câu hỏi.',
            ]);
        }

        $sheet = simplexml_load_string($sheetXml);
        if ($sheet === false) {
            throw ValidationException::withMessages([
                'import_file' => 'Không thể đọc dữ liệu trong file XLSX.',
            ]);
        }

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $column = $this->columnIndex((string) $cell['r']);
                $values[$column] = $this->cellValue($cell, $sharedStrings);
            }

            if ($values !== []) {
                ksort($values);
                $rows[] = array_values($values);
            }
        }

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    private function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $shared = simplexml_load_string($xml);
        if ($shared === false) {
            return [];
        }

        $strings = [];
        foreach ($shared->si as $item) {
            if (isset($item->t)) {
                $strings[] = (string) $item->t;
                continue;
            }

            $text = '';
            foreach ($item->r as $run) {
                $text .= (string) $run->t;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    /**
     * @param  array<int, string>  $sharedStrings
     */
    private function cellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) $cell['t'];

        if ($type === 's') {
            return trim($sharedStrings[(int) $cell->v] ?? '');
        }

        if ($type === 'inlineStr') {
            return trim((string) $cell->is->t);
        }

        return trim((string) $cell->v);
    }

    private function columnIndex(string $cellReference): int
    {
        preg_match('/^[A-Z]+/', $cellReference, $matches);
        $letters = $matches[0] ?? 'A';
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     * @return array<int, array{question_text: string, option_a: string, option_b: string, option_c: string, option_d: string, correct_option: string}>
     */
    private function normalizeRows(array $rows): array
    {
        $rows = array_values(array_filter($rows, fn ($row) => count(array_filter($row, fn ($value) => trim((string) $value) !== '')) > 0));

        if ($rows === []) {
            throw ValidationException::withMessages([
                'import_file' => 'File import chưa có dữ liệu câu hỏi.',
            ]);
        }

        $header = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[0]);
        $hasHeader = count(array_intersect($header, ['question_text', 'question', 'cau_hoi', 'noi_dung_cau_hoi'])) > 0;
        $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

        $questions = [];
        foreach ($dataRows as $index => $row) {
            $source = $hasHeader ? $this->mapHeaderRow($header, $row) : $this->mapFixedRow($row);
            $line = $hasHeader ? $index + 2 : $index + 1;

            $correctOption = mb_strtoupper(trim((string) ($source['correct_option'] ?? '')));
            if (! in_array($correctOption, ['A', 'B', 'C', 'D'], true)) {
                throw ValidationException::withMessages([
                    'import_file' => "Dòng {$line}: đáp án đúng phải là A, B, C hoặc D.",
                ]);
            }

            foreach (['question_text', 'option_a', 'option_b', 'option_c', 'option_d'] as $field) {
                if (trim((string) ($source[$field] ?? '')) === '') {
                    throw ValidationException::withMessages([
                        'import_file' => "Dòng {$line}: thiếu dữ liệu bắt buộc.",
                    ]);
                }
            }

            $questions[] = [
                'question_text' => trim((string) $source['question_text']),
                'option_a' => trim((string) $source['option_a']),
                'option_b' => trim((string) $source['option_b']),
                'option_c' => trim((string) $source['option_c']),
                'option_d' => trim((string) $source['option_d']),
                'correct_option' => $correctOption,
            ];
        }

        if ($questions === []) {
            throw ValidationException::withMessages([
                'import_file' => 'File import chưa có câu hỏi hợp lệ.',
            ]);
        }

        return $questions;
    }

    /**
     * @param  array<int, string>  $header
     * @param  array<int, string>  $row
     * @return array<string, string>
     */
    private function mapHeaderRow(array $header, array $row): array
    {
        $map = [];
        foreach ($header as $index => $key) {
            $map[$this->canonicalKey($key)] = $row[$index] ?? '';
        }

        return $map;
    }

    /**
     * @param  array<int, string>  $row
     * @return array<string, string>
     */
    private function mapFixedRow(array $row): array
    {
        return [
            'question_text' => $row[0] ?? '',
            'option_a' => $row[1] ?? '',
            'option_b' => $row[2] ?? '',
            'option_c' => $row[3] ?? '',
            'option_d' => $row[4] ?? '',
            'correct_option' => $row[5] ?? '',
        ];
    }

    private function normalizeHeader(string $value): string
    {
        return trim(preg_replace('/_+/', '_', preg_replace('/[^a-z0-9]+/', '_', Str::lower(Str::ascii($value)))), '_');
    }

    private function canonicalKey(string $key): string
    {
        return match ($key) {
            'question', 'cau_hoi', 'noi_dung_cau_hoi', 'noi_dung' => 'question_text',
            'a', 'answer_a', 'dap_an_a' => 'option_a',
            'b', 'answer_b', 'dap_an_b' => 'option_b',
            'c', 'answer_c', 'dap_an_c' => 'option_c',
            'd', 'answer_d', 'dap_an_d' => 'option_d',
            'correct', 'correct_answer', 'dap_an_dung' => 'correct_option',
            default => $key,
        };
    }
}
