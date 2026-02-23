<?php

namespace App\Helpers;

class phpspreadsheet
{
    public static function addFullBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addBorderDottedHorizontal($spreadsheet, $range, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color' => ['argb' => $color],
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color' => ['argb' => $color],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'vertical' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }


    public static function addHeaderBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'bottom' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'left' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'right' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'vertical' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'horizontal' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addHorizontalVerticalBorder($spreadsheet, $range, $verticalStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, $horizontalStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => $horizontalStyle,
                    'color' => ['argb' => $color],
                ],
                'bottom' => [
                    'borderStyle' => $horizontalStyle,
                    'color' => ['argb' => $color],
                ],
                'left' => [
                    'borderStyle' => $verticalStyle,
                    'color' => ['argb' => $color],
                ],
                'right' => [
                    'borderStyle' => $verticalStyle,
                    'color' => ['argb' => $color],
                ],
                'vertical' => [
                    'borderStyle' => $verticalStyle,
                    'color' => ['argb' => $color],
                ],
                'horizontal' => [
                    'borderStyle' => $horizontalStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addBorderDottedMiddleHorizontal($spreadsheet, $range, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'vertical' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addInlineBorderDotted($spreadsheet, $range, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'vertical' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color' => ['argb' => $color],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addOutlineBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addVerticalBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'vertical' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addTopBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addBottomBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    // border titik-titik
    public static function addFullBorderDotted($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addBottomBorderDotted($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addMiddleBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'vertical' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'horizontal' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function addXHorizontalVerticalBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'bottom' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => $color],
                ],
                'vertical' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
                'horizontal' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public static function styleFont($spreadsheet, $range, $bold = false, $size = 12, $font = 'Calibri', $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => $bold,
                'size' => $size,
                'name' => $font,
                'color' => ['argb' => $color],
            ],
        ]);
    }

    public static function styleCell($spreadsheet, $range, $bgColor = 'FFFFFFFF')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => $bgColor,
                ],
            ],
        ]);
    }

    /**
     * Fill a (merged) cell with dot characters to create a dotted-box appearance.
     * Uses the top-left cell of the given range to set the value.
     */
    public static function fillWithDots($spreadsheet, $range, $dot = '.', $repeat = 30, $lines = 3, $fontSize = 8, $font = 'Tahoma')
    {
        $parts = explode(':', $range);
        $startCell = trim($parts[0]);
        $dotLine = str_repeat($dot, max(1, (int) $repeat));
        $valueLines = array_fill(0, max(1, (int) $lines), $dotLine);
        $value = implode(PHP_EOL, $valueLines);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue($startCell, $value);
        $sheet->getStyle($range)->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => false,
                'size' => $fontSize,
                'name' => $font,
            ],
        ]);
    }

    public static function numberFormatCommaSeparated($spreadsheet, $range, $decimal = 2)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)
            ->getNumberFormat()
            ->setFormatCode('#,##0.' . str_repeat('0', $decimal));
    }

    public static function numberFormatCommaThousandsOrZero($spreadsheet, $range, $decimal = 2)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->getNumberFormat()->setFormatCode('#,##0.' . str_repeat('0', $decimal) . ';-#,##0.' . str_repeat('0', $decimal) . ';"-"');
    }

    public static function numberFormatThousands($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)
            ->getNumberFormat()
            ->setFormatCode('#,###');
    }

    public static function numberPercentage($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
    }

    public static function numberPercentageOrZero($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->getNumberFormat()->setFormatCode('0.00%;-0.00%;"-"');
    }

    public static function numberFormatThousandsOrZero($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
    }

    public static function textAlignCenter($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    public static function textAlignLeft($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    public static function textAlignRight($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    public static function textRotateUp($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->getAlignment()
            ->setTextRotation(90)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }
}
