<?php

namespace cszchen\citizenid;

/**
 * 
 * @author cartmanchen csz@soyhw.com
 * @date   2014
 * 中国大陆居民身份证验证
 * 可以获得身份证的性别，出生年月日，地区
 * 通过CityCode类的getPlace方法，可以获得省市县的中文表达。
 * 
 * @example
 * $id = new CitizenIdNumber(420323198611103512);
 * $id->isValidate();
 * 
 * or
 * 
 * id = new CitizenIdNumber()
 * $id->isValidate(420323198611103512);
 *
 */
class Parser
{
    const GENDER_MALE = 1;
    
    const GENDER_FEMALE = 0;

    /**
     *
     * @var string 身份证号码
     */
    protected $idNumber = null;

    /**
     *
     * @var int 身份号码的长度
     */
    protected $idLength;
    
    protected $isValidate = false;
    
    protected $cityCode = [];

    /**
     *
     * @var array 加权因子
     */
    protected $salt = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];

    /**
     *
     * @var array 校验码
     */
    protected $checksum = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];

    /**
     * 
     * @param string $id
     * @throws \Exception
     */
    public function setId($id)
    {
        if (!$id) {
            throw new \Exception('param "id" must not be null.');
        }
        if (!is_string($id)) {
            throw new \Exception('the type of param $id must be string.');
        }
        $this->idNumber = trim($id);//var_dump($this->idNumber);
        $this->idLength = strlen($id);
        $this->isValidate = false;
    }

    /**
     * 验证号码是否合法
     * 
     * @param string $id            
     * @return boolean
     */
    public function isValidate($id = null)
    {
        if ($this->isValidate) {
            return true;
        }
        if (!empty($id)) {
            $this->setId($id);
        }
        
        if (empty($this->idNumber)) {
            throw new \Exception('Id number must be set.');
        }
        
        if ($this->checkFormat()
            && $this->checkBirthday() 
            && $this->checkLastCode()) {
            $this->isValidate = true;
            return true;
        }
        
        return false;
    }

    /**
     * 获取出生年月日,格式 Ymd
     * 
     * @return string
     */
    public function getBirthday()
    {
        if ($this->idLength == 18) {
            $birthday = substr($this->idNumber, 6, 8);
        } else {
            $birthday = '19' . substr($this->idNumber, 6, 6);
        }
        return $birthday;
    }

    /**
     * 获取性别
     * 
     * @return int
     */
    public function getGender()
    {
        if ($this->idLength == 18) {
            $gender = $this->idNumber{16};
        } else {
            $gender = $this->idNumber{14};
        }
        return $gender % 2 == 0 ? self::GENDER_FEMALE : self::GENDER_MALE;
    }

    /**
     * 检查号码格式
     * 
     * @return boolean
     */
    protected function checkFormat()
    {
        if (! preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $this->idNumber)) {
            return false;
        }
        return true;
    }

    /**
     * 检查出生年月
     * 
     * @return boolean
     */
    protected function checkBirthday()
    {
        $birthday = $this->getBirthday();
        $pattern = '/(([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]{1}|[0-9]{1}[1-9][0-9]{2}|[1-9][0-9]{3})(((0[13578]|1[02])(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)(0[1-9]|[12][0-9]|30))|(02(0[1-9]|[1][0-9]|2[0-8]))))|((([0-9]{2})(0[48]|[2468][048]|[13579][26])|((0[48]|[2468][048]|[3579][26])00))0229)/';
        if (!preg_match($pattern, $birthday)) {
            return false;
        }
        return true;
    }

    /**
     * 校验最后一位校验码
     * 
     * @return boolean
     */
    protected function checkLastCode()
    {
        if ($this->idLength == 15) {
            return true;
        }
        $sum = 0;
        $number = (string) $this->idNumber;
        for ($i = 0; $i < 17; $i ++) {
            $sum += $number{$i} * $this->salt{$i};
        }
        $seek = $sum % 11;
        if ((string) $this->checksum[$seek] !== strtoupper($number{17})) {
            return false;
        }
        return true;
    }
}
