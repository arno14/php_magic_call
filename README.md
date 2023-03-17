MAGIC_CALL LIBRARY


Using the trait MagicCallTrait, protected and private properties can be accessed via magic getter/setter.

override the method configureMagicCall to define which property should be accessed

The read-write props can also be guessed from the php doc

see test for usage


@todo:
- documentation
  
- redirect property-read and property-write to existing getter/setter
- method read and write (getter and setter)
  
- caching Callconfig
- caching Callconfig via PSR Cache Interface
  
- global configuration
- global configuration by env vars
  
- phpdoc extractor in dedicated class
- phpdoc from other libraries
  
- Exception class
