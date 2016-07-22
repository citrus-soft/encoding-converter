# Плагин composer для конвертации пакетов в кодировку сайта
  
Пакеты для битрикса могут быть написаны в разных кодировках (`utf-8` или `windows-1251`). Чтобы иметь использовать эти пакеты на битрикс-сайтах с произвольной кодировкой можно использовать этот плагин.

## Как использовать?

Допустим, сайт работает в `utf-8`, а нужный нам пакет `citrus/iblock.element.form` написан в `windows-1251`.

Добавим `citrus/encoding-converter` как зависимость и укажем в ключе `extra.encoding-convert` название пакета и кодировку, в которую его нужно сконвертировать:

```
{
    "require": {
        "php": ">=5.3",
        "citrus/iblock.element.form": "0.*",
        "citrus/encoding-converter": "^0.1"
    },
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.citrus-dev.ru"
        },
    ],
    "extra": {
    	"encoding-convert": {
    		"citrus/iblock.element.form": "utf-8"
    	}
    }
}
```

## Дополнительные возможности

После установки плагин можно использовать для перекодирования `lang-`файлов модуля, шаблона или компонента из коммандной строки: `composer encoding:convert [options] [--] <path> <to> [<from>]`

```
~
$ composer help encoding:convert
Usage:
  encoding:convert [options] [--] <path> <to> [<from>]

Arguments:
  path                           Directory in which to perform conversion (i.e. path to component, template or module)
  to                             Target encoding
  from                           Source encoding (if ommited, conversion performed between utf-8 and windows-1251)

Options:
  -l, --lang[=LANG]              Language to convert (for bitrix lang files) (default is ru)
  -a, --all[=ALL]                Process all php files (not ony bitrix lang files)
  -h, --help                     Display this help message
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi                     Force ANSI output
      --no-ansi                  Disable ANSI output
  -n, --no-interaction           Do not ask any interactive question
      --profile                  Display timing and memory usage information
      --no-plugins               Whether to disable plugins.
  -d, --working-dir=WORKING-DIR  If specified, use the given directory as working directory.
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
 Convert encoding of bitrix lang files (*.php in lang/<lang_code>/ subfolders)

```