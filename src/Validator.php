<?php

declare(strict_types=1);

namespace Localzet\WebantPractice1;

/**
 * Практическое задание от WebAnt №1
 * 
 * @package     WebAnt-Practice-1
 * @link        https://github.com/localzet/WebAnt-Practice-1
 * @author      Ivan Zorin <creator@localzet.com>
 * 
 * Для написания использованы следующие материалы:
 * @see https://en.wikipedia.org/wiki/Payment_card_number
 * @see https://en.wikipedia.org/wiki/Luhn_algorithm
 * @see https://gist.github.com/michaelkeevildown/9096cd3aac9029c4e6e05588448a8841
 */

class Validator
{
    /**
     * Проверка контрольной суммы банковской карты с помощью алгоритма Луна
     * 
     * @param string $cardNumber Номер банковской карты
     * @return bool Возвращает true, если контрольная сумма карты действительна, false - если нет
     */
    public static function validateChecksum(string $cardNumber): bool
    {
        // Удаляем пробелы и дефисы из номера карты
        $cardNumber = str_replace([' ', '-'], '', $cardNumber);

        // Проверяем длину номера карты
        $cardLength = strlen($cardNumber);
        if ($cardLength != 16 && $cardLength != 14) {
            return false;
        }

        // Проверяем тип карты
        $cardType = static::getCardType($cardNumber);
        if (!$cardType) {
            return false;
        }

        // Разбиваем строку для посимвольной обработки
        $digits = str_split($cardNumber);
        $sum = 0;
        $odd = true;

        // Алгоритм Луна
        foreach ($digits as $digit) {
            // Всё должно быть цифрами
            if (!is_numeric($digit)) {
                return false;
            }

            // Преобразуем
            $digit = (int) $digit;

            if ($odd) {
                // Если текущая цифра является нечетной, удваиваем её значение
                $digit *= 2;
                if ($digit > 9) {
                    // Если удвоенное значение превышает 9 - вычитаем 9
                    $digit -= 9;
                }
            }

            // Складываем с общей суммой
            $sum += $digit;

            // "Переключатель", ибо нам нужны только нечётные позиции
            $odd = !$odd;
        }

        return ($sum % 10 == 0);
    }

    /**
     * Определяет тип банковской карты
     *
     * @param string $cardNumber Номер банковской карты
     * @return string|false Тип банковской карты или false, если типа не существует
     */
    public static function getCardType(string $cardNumber): string|false
    {
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
            // VISA начинается с "4" и может содержать 13 или 16 цифр
            return 'VISA';
        } elseif (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber)) {
            // MasterCard начинается с "51"-"55" и содержит 16 цифр
            return 'MasterCard';
        } elseif (preg_match('/^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/', $cardNumber)) {
            // Maestro обычно начинается с "50", "56"-"58" или "6304" и может содержать от 12 до 19 цифр
            return 'Maestro';
        } elseif (preg_match('/^(148199)\d{8}$/', $cardNumber)) {
            // Даронь Кредит начинается с "148199" и содержит 14 цифр
            return 'Даронь Кредит';
        }

        return false;
    }
}
