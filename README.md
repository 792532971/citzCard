检验一个身份证号码的格式是否正确，并且可以获取到生日，性别，办证区域

安装
===

`composer require cszchen/citizenid`

使用
===

```php
use cszchen\citizenid\Parser;
    
$parser = new Parser();
$parser->setId($id);
    
//身份证号码格式是否正确
$parser->isValidate();
    
//获取生日，格式YYYYmmdd
$parser->getBirthday();
    
//获取性别, 1-男， 0-女，对应的常量为Parser::GENDER_MALE, Parser::GENDER_FEMALE
$parser->getGender()
    
```	
