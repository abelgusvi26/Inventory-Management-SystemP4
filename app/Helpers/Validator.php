<?php
/**
 * Validator Server-Side untuk Validasi Sintaksis dan Semantik
 */

class Validator {
    private $errors = [];
    private $data = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function required($field, $label) {
        $val = trim((string)(isset($this->data[$field]) ? $this->data[$field] : ''));
        if ($val === '') {
            $this->errors[$field] = "{$label} wajib diisi.";
        }
        return $this;
    }

    public function minLength($field, $min, $label) {
        $val = trim((string)(isset($this->data[$field]) ? $this->data[$field] : ''));
        if ($val !== '' && mb_strlen($val) < $min) {
            $this->errors[$field] = "{$label} minimal berukuran {$min} karakter.";
        }
        return $this;
    }

    public function maxLength($field, $max, $label) {
        $val = trim((string)(isset($this->data[$field]) ? $this->data[$field] : ''));
        if ($val !== '' && mb_strlen($val) > $max) {
            $this->errors[$field] = "{$label} maksimal berukuran {$max} karakter.";
        }
        return $this;
    }

    public function positiveInteger($field, $label, $allowZero = false) {
        $raw = isset($this->data[$field]) ? $this->data[$field] : null;
        if ($raw === null || $raw === '') {
            return $this;
        }

        $val = filter_var($raw, FILTER_VALIDATE_INT);
        if ($val === false) {
            $this->errors[$field] = "{$label} harus berupa bilangan bulat.";
        } elseif ($allowZero && $val < 0) {
            $this->errors[$field] = "{$label} tidak boleh bernilai negatif.";
        } elseif (!$allowZero && $val <= 0) {
            $this->errors[$field] = "{$label} harus bernilai lebih dari nol.";
        }
        return $this;
    }

    public function numericNonNegative($field, $label) {
        $raw = isset($this->data[$field]) ? $this->data[$field] : null;
        if ($raw === null || $raw === '') {
            return $this;
        }

        if (!is_numeric($raw) || (float)$raw < 0) {
            $this->errors[$field] = "{$label} harus berupa nilai nominal positif atau nol.";
        }
        return $this;
    }

    public function inArray($field, array $allowed, $label) {
        $val = isset($this->data[$field]) ? $this->data[$field] : '';
        if (!in_array($val, $allowed, true)) {
            $this->errors[$field] = "Pilihan {$label} tidak valid.";
        }
        return $this;
    }

    public function addCustomError($field, $message) {
        $this->errors[$field] = $message;
        return $this;
    }

    public function isValid() {
        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getFirstError() {
        return reset($this->errors) ?: null;
    }
}
