поля посылок:

[action] - описание типа посылки
  'sent' - отправить .tar.gz архив лабораторной Подёнку
  'downloadTar' - скачать созданный .tar.gz архив лабораторной
  'downloadFile' - скачать файл из архива лабораторной
  'info' - запрос информации о лабораторной

[id] - код лабораторной
  '...' - 32-символьный код если запрос к существующей лабораторной
  '0' - создание новой лабораторной
[files] - двумерный массив файлов, первое поле одно из описанный далее, второе поле - порядковый номер загружаемого файла.
  [name]
  [tmp_name]
  [size]
  [error]
