<?php

namespace App\Libraries;

class Request
{
    private $query;
    private $request;
    private $attributes;
    private $cookies;
    private $files;
    private $server;
    private $content;
    private $errors;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->query = $query;
        $this->request = $request;
        $this->attributes = $attributes;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->content = $content;
        $this->errors = array();
    }

    public static function createFromGlobals()
    {
        return new static($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, file_get_contents('php://input'));
    }

    public function query($key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function request($key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }

    public function attributes($key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    public function cookies($key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    public function files($key, $default = null)
    {
        return $this->files[$key] ?? $default;
    }

    public function server($key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    public function getContent()
    {
        return (object)json_decode($this->content, true);
    }

    public function validate($data, $rules)
    {
        foreach ($rules as $field => $rule) {
            $ruleParts = explode('|', $rule);
            $value = $data[$field] ?? null;

            foreach ($ruleParts as $rulePart) {
                $this->applyRule($field, $rulePart, $value, $data);
            }
        }

        return $this->errors;
    }

    private function applyRule($field, $rulePart, $value, $data)
    {
        $param = null;
        if (strpos($rulePart, ':') !== false) {
            list($rulePart, $param) = explode(':', $rulePart);
        }

        switch ($rulePart) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, ucfirst($field) . ' alanı zorunludur.');
                }
                break;
            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, ucfirst($field) . ' alanı sayısal olmalıdır.');
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, ucfirst($field) . ' alanı geçerli bir e-posta adresi olmalıdır.');
                }
                break;
            case 'max':
                if (strlen($value) > $param) {
                    $this->addError($field, ucfirst($field) . " alanı en fazla $param karakter olabilir.");
                }
                break;
            case 'min':
                if (strlen($value) < $param) {
                    $this->addError($field, ucfirst($field) . " alanı en az $param karakter olmalıdır.");
                }
                break;
            case 'alpha':
                if (!ctype_alpha($value)) {
                    $this->addError($field, ucfirst($field) . ' alanı sadece harf içermelidir.');
                }
                break;
            case 'alpha_num':
                if (!ctype_alnum($value)) {
                    $this->addError($field, ucfirst($field) . ' alanı yalnızca harf ve rakam içermelidir.');
                }
                break;
            case 'unique':
                // Burada veritabanında alanın benzersiz olup olmadığını kontrol etme işlemi yapılabilir.
                break;
            case 'date':
                if (!strtotime($value)) {
                    $this->addError($field, ucfirst($field) . ' alanı geçerli bir tarih formatı içermelidir.');
                }
                break;
            case 'in':
                $options = explode(',', $param);
                if (!in_array($value, $options)) {
                    $this->addError($field, ucfirst($field) . " alanı yalnızca [$param] içeren değerlerden biri olmalıdır.");
                }
                break;
            case 'confirmed':
                $confirmationField = $field . '_confirm';
                if (strcasecmp($value, $data[$confirmationField]) !== 0) {
                    $this->addError($field, ucfirst($field) . ' alanı doğrulama alanıyla eşleşmiyor.');
                }
                break;
            case 'phone':
                // Telefon numarası sadece sayılar içermelidir, 10-15 karakter uzunluğunda olmalıdır.
                if (!preg_match('/^\d{10,15}$/', $value)) {
                    $this->addError($field, ucfirst($field) . ' alanı geçerli bir telefon numarası olmalıdır.');
                }
                break;
            case 'file':
                if (!isset($_FILES[$field]) || $_FILES[$field]['error'] != UPLOAD_ERR_OK) {
                    $this->addError($field, ucfirst($field) . ' alanı geçerli bir dosya içermelidir.');
                    break;
                }

                $file = $_FILES[$field];

                // Dosya boyutu kontrolü (parametre megabayt cinsinden verilmeli, örn: max:2 için 2 MB).
                if ($param) {
                    list($fileRule, $fileParam) = explode(':', $param);
                    if ($fileRule == 'max') {
                        $maxSize = $fileParam * 1024 * 1024; // Megabaytı byte'a çevir.
                        if ($file['size'] > $maxSize) {
                            $this->addError($field, ucfirst($field) . " alanı en fazla $fileParam MB boyutunda olabilir.");
                        }
                    }
                }

                // Dosya türü kontrolü (izin verilen MIME türleri virgülle ayrılmış olarak verilmeli).
                if ($param && strpos($param, 'mimes:') === 0) {
                    $allowedMimes = explode(',', str_replace('mimes:', '', $param));
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $fileMime = $finfo->file($file['tmp_name']);
                    if (!in_array($fileMime, $allowedMimes)) {
                        $this->addError($field, ucfirst($field) . ' alanı geçerli bir dosya türü içermelidir. İzin verilen türler: ' . implode(', ', $allowedMimes) . '.');
                    }
                }
                break;
            case 'accepted':
                if (!in_array(strtolower($value), ['on', 'yes', '1', 'true'])) {
                    $this->addError($field, ucfirst($field) . ' alanı kabul edilmelidir.');
                }
                break;
        }
    }

    private function addError($field, $message)
    {
        $this->errors[$field] = $message;
    }
}
