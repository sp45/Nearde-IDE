<?php
namespace nd\forms;

use std, gui, framework, nd;


class openProjectForm extends AbstractForm
{
    /**
     * @var TimerScript
     */
    public $timerAdd;// таймер
    public $pos;// номер страницы
    public $data;// дата
    public $result;// результат поиска
    public $add;
    public $type;// тип сортировки
    private $timerpos;// количество повторов последний операции таймера
    private $timerAdd_date;// массив который надо превратить в панельки

    public $speed = 50;
    public $count = 9;
    
    /**
     * @event construct
     */
    function doConstruct(UXEvent $e = null)
    {

        $settings = file_get_contents('settings.txt');
        $set = explode('|', $settings);
        $this->speed = $this->numberField4->value = $set[1];// настройки
        $this->count = $this->numberField3->value = $set[0];

        //$get = $this->update_date();// получаем дату
        $date = [['name' => 'name1', 'version' => '0.16.7', 'context' => 'context1', 'date' => '06.04.2018', 'tags' => 'tag1', 'image' => 'E:\Projects DN\заказ\src\.data\img\No_image.png'], ['name' => 'name2', 'version' => '0.16.75', 'context' => 'context2', 'date' => '06.06.2018', 'tags' => 'tag2', 'image' => 'E:\Projects DN\заказ\src\.data\img\No_image.png']];
        $this->data = $this->result = $date;
        $get  = $this->add_goods_from_array($date);
        $this->pagination->total = ceil(count($get) / $this->count);
        $this->pos = 0;

        // pre($this->sort_text($this->convert_to_int_array())); 


    }
    
    /**
     * Поиск совпадений
     */
    function search_goods($array, $search, $type = 'name')
    {
        $this->result = [];// обнуляем предыдущий результат
        foreach ($array as $k => $v) {
            $res = strrpos($v[$type], $search) . 'f';// добавляем любую букву для того что-бы если была пустота то пропустить это слово
            if ($res != 'f') {// проверяем
                $result[] = $array[$k];// добавляем к переменной которую вернем
                $this->result[] = $array[$k];// результат
            }
        }
        return $result;

    }

    /**
     * Добавляем товар с помощью таймера
     */
    function add_goods_from_array($array, $path_image = 'src/.data/img/')
    {
        $this->tilePaneAlt->children->clear();// очистка
        $this->add = $array;
        if (isset($this->timerAdd)) {// проверяем есть ли таймер
            $this->timerAdd->stop();// останавливаем, на всякий пожарный
            $this->timerpos = 0;// обнуляем количество повторов таймера
            $this->timerAdd_date = $array;// передаем массив
            $this->timerAdd->start();// запускаем таймер
        } else {
            $this->timerAdd = new TimerScript;// создаем таймер
            $this->timerAdd->interval = $this->speed;// не рекомендуется ставить меньше 20
            $this->timerAdd->repeatable = 1;// повторять, не менять
            $this->timerpos = 0;// обнуляем количество повторов таймера
            $this->timerAdd_date = $array;// передаем массив
            $this->timerAdd->on('action', function () use ($this, $path_image) {// задаем действие
                $array = $this->timerAdd_date;// лень переписывать было
                $i = $this->timerpos + $this->pos * $this->count;// типа цикл :) , узнаем с какова места добавлять товар
                if ($this->timerpos < $this->count and isset($array[$i])) {
                    $img_path = $path_image . $array[$i]['image'];
                    $img = (fs::exists($img_path)) ? $img_path : 'src/.data/img/No_image.png';
                    // var_dump($img);
                    // само добавление
                    $good = $this->add_goods($array[$i]['name'], $array[$i]['context'], $array[$i]['version'], $array[$i]['date'], $array[$i]['tags'], $array[$i]['rei'], $img);
                    $this->tilePaneAlt->add($good);
                } else {
                    $this->timerAdd->stop();// останавливаем таймер что-бы не нагружал процессор
                }
                $this->timerpos += 1;
            });
            $this->timerAdd->start();// запускаем
        }
        $this->containerAlt->scrollY = 0;// переходим в самый верх
    }

    /**
     * Поиск
     * @event button3.action
     */
    function search_button(UXEvent $e = null)
    {
        $search = $this->edit3->text;// слово
        $type = $this->type;// тип
        $this->search_and_sort($search, $type);
    }

    function search_and_sort($text, $type = 'name', $tag_ser = 0)
    {
        $search = $text;// слово
        $data = $this->data;// данные
        $serch_type = (isset($tag_ser)) ? 'tag' : '';
        if ($text != null) {// проверяем что-бы не было ошибок
            $this->search_goods($data, $search, $type);// сам поиск
            if (isset($this->result[0])) {
                $this->sort_and_show($this->result, $this->reverse->selected, $type);// загрузка
                $this->pagination->total = ceil(count($this->result) / $this->count);// считаем страницы
            } else {
                $this->tilePaneAlt->children->clear();// убираем всё
                $this->pagination->total = ceil(count($this->result) / $this->count);// считаем страницы
            }
        } else {
            $this->sort_and_show($data, $this->reverse->selected, $type);// загружаем
            $this->pagination->total = ceil(count($data) / $this->count);// считаем страницы
        }
    }

    /**
     * Переключение страниц
     * @event pagination.action
     */
    function page_panel(UXEvent $e = null)
    {
        $this->pos = $e->sender->selectedPage;// устанавливаем номер страницы
        $this->add_goods_from_array($this->add);// загрузка
        $this->containerAlt->scrollY = 0;
    }

    /**
     * @event edit3.construct
     */
    function search_edit(UXEvent $e = null)
    {
        $e->sender->observer('text')->addListener(function () use ($this, $e) {
            $search = $e->sender->text;// слово
            $data = $this->data;// данные
            if ($this->checkbox->selected) {
                // $e->sender->enabled = 0;
                if ($e->sender->text != null) {// проверяем что-бы не было ошибок
                    $this->search_goods($data, $search);// поиск
                    if (isset($this->result[0])) {// проверяем что-бы не было ошибок
                        $this->sort_and_show($this->result, $this->reverse->selected, $this->type);// загрузка
                        $this->pagination->total = ceil(count($this->result) / $this->count);// считаем страницы
                    } else {
                        $this->tilePaneAlt->children->clear();// убираем всё
                        $this->pagination->total = ceil(count($this->result) / $this->count);// считаем страницы
                    }
                } else {
                    $this->sort_and_show($data, $this->reverse->selected, $this->type);// загрузка
                    $this->pagination->total = ceil(count($data) / $this->count);// считаем страницы
                }
                waitAsync(100, function () use ($e) {
                    // $e->sender->enabled = 1;
                    $e->sender->requestFocus();// переводим фокус
                    // $e->sender->deselect();
                    $e->sender->end();// переносим в конец
                });
            }
        });
    }

    /**
     * Сортирует цифровые значения
     */
    function sort_num($arrStrings)
    {
        foreach ($arrStrings as $k => $v) {
            $res[$v][] = $v;
        }
        $res = arr::sortByKeys($res);
        return $this->get_value_with_arrays($res);
    }

    /**
     * Сортирует и показывает панельки из массива
     */
    function sort_and_show($array, $reverse = 0, $type = 'name')
    {
        foreach ($array as $k => $v) {
            $arrayy[$v[$type]] = $v;// задаем ключ как значение
            $arrayID[$v[$type]][] = $k;// добавляем в группу по значению
        }

        // var_dump($arrayID);// debug

        foreach ($arrayy as $k => $v) {
            $arrStrings[] = $v[$type];// делаем строку для сортировки
        }

        if ($type == 'amout') {
            $sort = $this->sort_num($arrStrings);// используем другой метод сортировки
        } else {
            $conv = $this->convert_to_int_array($arrStrings);// переводим буквы в цифры
            $sort = $this->sort_text($conv);// сортируем
        }

        foreach ($sort[1] as $k => $v) {
            foreach ($arrayID[$v] as $key => $val) {
                $res[] = $array[$val];// применяем сортировку
                // var_dump($array[$val]['name'].' '.$val);
            }
        }
        // var_dump($conv);
        if ($type == 'rei') {
            $res = arr::reverse($res);// если надо чтобы сперва самые худшие ,а потом лучшие то надо убрать эту строку
            // короче переворачиваем полученный результат
        }

        if ($reverse) {
            // $res = arr::reverse($res);
        }

        // pre($res);

        $this->add_goods_from_array($res);// загружаем
    }


    /**
     * --RU--
     * Переводить текст в массив с числами, неизвестные символы будут заменены на "-2"
     */
    function convert_to_int_array($strings = ['01', '02', '11', '12', '09', '20'])
    {

        // Массив с числовым id букв
        $num = ['1' => ' ', '2' => '!', '3' => '|', '4' => '_', '5' => '-', '6' => '=', '7' => '+', '8' => 'я', '9' => 'ю', '10' => 'э', '11' => 'ь', '12' => 'ы', '13' => 'ъ', '14' => 'щ', '15' => 'ш', '16' => 'ч', '17' => 'ц', '18' => 'х', '19' => 'ф', '20' => 'у', '21' => 'т', '22' => 'с', '23' => 'р', '24' => 'п', '25' => 'о', '26' => 'н', '27' => 'м', '28' => 'л', '29' => 'к', '30' => 'й', '31' => 'и', '32' => 'з', '33' => 'ж', '34' => 'ё', '35' => 'е', '36' => 'д', '37' => 'г', '38' => 'в', '39' => 'б', '40' => 'а', '41' => 'z', '42' => 'y', '43' => 'x', '44' => 'w', '45' => 'v', '46' => 'u', '47' => 't', '48' => 's', '49' => 'r', '50' => 'q', '51' => 'p', '52' => 'o', '53' => 'n', '54' => 'm', '55' => 'l', '56' => 'k', '57' => 'j', '58' => 'i', '59' => 'h', '60' => 'g', '61' => 'f', '62' => 'e', '63' => 'd', '64' => 'c', '65' => 'b', '66' => 'a', '67' => '0', '68' => '9', '69' => '8', '70' => '7', '71' => '6', '72' => '5', '73' => '4', '74' => '3', '75' => '2', '76' => '1', 'dev' => 'brend'];
        foreach ($strings as $k => $v) {// перебираем передаваемые строки
            $res = [];// обнуляем результат
            $t = 0;// обнуляем повторы
            if (isset($v)) {
                $text = explode('', str::lower($v));
                unset($text[arr::lastKey($text)]);// убираем побочный эффект функции explode

                foreach ($text as $kt => $vt) {
                    foreach ($num as $kn => $vn) {// заменяем буквы на цифры
                        $vn0 = $vn . 'p';
                        $vt0 = $vt . 'p';
                        if ($vt0 == $vn0) {
                            $res[] = $kn;
                            $t++;
                        }
                    }

                    if ($t == 0) {
                        $res[] = '-2';// присваиваем id если такого символа нет в массиве
                    } else {
                        $t = 0;// сбрасываем счетчик
                    }
                }

                $conv[$v] = $res;
            }
        }
        return $conv;
    }

    function sort_text($arrKey, $Array = null, $pos = '0', $getVal = 1)
    {
        if ($getVal) {// проверяем не выполнялась фунцыя до этого
            $this->serched = [];// обнуляем массив с использованными элементами
            foreach ($arrKey as $k => $v) {// проверяем каждый элемент массива
                if (isset($v[$pos])) {// проверяем: символ не равен null?
                    $group[$v[$pos]][] = $k;// добавляем в массив с id данного символа
                } else {
                    $ser = 0;// обнуляем переменную чтобы
                    foreach ($this->serched as $key => $val) {// проверяем каждый элемент массива
                        if ($k == $val) {// сравниваем элементы
                            $ser = 1;// устанавливаем что нашли совпадение
                            // unset($group[$pos]);
                        }
                    }
                    if ($ser == 0) {
                        $group['N'][] = $k;// добавляем в группу N
                        $this->serched[] = $k;// добавляем в массив с уже использованными элементами
                        $ser = 0;
                    }
                }
            }
        } else {
            foreach ($Array as $k => $v) {
                if (isset($v[$pos])) {
                    $group[$v[$pos]][] = $arrKey[$v];
                } else {
                    $ser = 0;
                    foreach ($this->serched as $key => $val) {
                        if ($v == $val) {
                            $ser = 1;
                            // unset($group[$pos]);
                        }
                    }
                    if ($ser == 0) {
                        $group['N'][] = $v;
                        $this->serched[] = $v;
                        // $ser = 0;
                    }
                }
            }
        }
        // var_dump($group);
        foreach ($group as $k => $v) {
            // Logger::warn('----------------');// debug
            // var_dump($group);// debug
            // pre($group);
            if ($k != 'N') {// пропускаем если пустота
                if (count($v) > 1) {// проверяем нет ли совпадений
                    $group[$k] = $this->sort_text($arrKey, $group[$k], $pos + 1, 0);
                }
            }
            // Logger::debug('----------------');
            // var_dump($group);
        }
        $group = arr::sortByKeys($group);// сортируем
        $group = arr::reverse($group);// переворачиваем

        if ($getVal) {
            $group = $this->get_value_with_arrays($group);// получаем значения
        }

        return $group;// возвращаем результат
        // var_dump($group);
    }

    /**
     * Получает все значения массива
     */
    function get_value_with_arrays($array)
    {
        $res = [];// сбрасываем
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $t = implode('||', $this->get_value_with_arrays($v));
                // var_dump($t);// debug
                $res[0] .= str_replace('||Array', '', $t);// убираем лишние
                // var_dump($res);
            } else {
                $res[0] .= $v . '||||';
                // var_dump($array);
            }
            $exp = explode('||||', $res[0]);// разбиваем строку
            unset($exp[arr::lastKey($exp)]);// удаляем ненужный компонент
            foreach ($exp as $key => $val) {
                if ($val == null) {
                    unset($exp[$key]);// убираем пустоту
                }
            }
            $res[1] = $exp;
        }

        return $res;
    }

    function add_goods($name, $context, $version, $data, $tag, $reiting, $image)
    {
        $good = new UXPanel();
        $good->size = [230, 107];
        // $good->position = [170, 0];
        $good->borderWidth = 0;
        // ----------------Image----------------------
        $imagea = new UXImageArea(new UXImage($image));
        $imagea->size = [230, 107];
        $imagea->stretch = 1;
        // $image_rect->strokeWidth = 1;
        // ---------------Text_background-------------
        $text_rect = new UXScrollPane();
        $text_rect->size = [230, 36];
        $text_rect->position = [0, 71];
        $text_rect->hbarPolicy = $text_rect->vbarPolicy = NEVER;
        // $text_rect->style = '-fx-base:black;';
        $text_rect->style = '-fx-base:transparent;';

        $image_r = new UXImageArea(new UXImage($image));
        $image_r->size = [230, 107];
        $image_r->position = [0, -70];
        $image_r->stretch = 1;
        $blur = new GaussianBlurEffectBehaviour();
        $blur->radius = 14.6;// change
        $blur->apply($image_r);

        $text_name_r = new UXLabel($name);
        $text_name_r->font->family = 'Franklin Gothic Medium';// change
        $text_name_r->font->size = '14';// change
        $text_name_r->textColor = white;
        // $text_name_r->backgroundColor = UXColor::rgb(0, 0, 0, 0.2);// change
        $text_name_r->size = [105, 20];
        $text_name_r->position = [6, 8];

        $text_amout_r = new UXLabel('Версия: ' . $version);
        $text_amout_r->font->family = 'Franklin Gothic Medium';// change
        $text_amout_r->font->size = '14';// change
        $text_amout_r->textColor = white;
        $text_amout_r->alignment = BASELINE_RIGHT;
        // $text_amout_r->backgroundColor = UXColor::rgb(0, 0, 0, 0.2);// change
        // $text_amout_r->style = '-fx-font-family: Franklin Gothic Medium;-fx-font-size: 14.0;-fx-font-weight: normal;-fx-font-style: normal;';
        $text_amout_r->size = [105, 20];
        $text_amout_r->position = [120, 8];

        $cont_text_rect = new UXPanel();
        //$cont_text_rect->children->addAll([$image_r, $text_name_r]);
        $cont_text_rect->children->addAll([$image_r, $text_amout_r, $text_name_r]);

        $text_rect->content = $cont_text_rect;

        // ---------------big_text_background-------------
        $big_text_rect = new UXScrollPane();
        $big_text_rect->size = [230, 107];
        $big_text_rect->position = [0, 0];
        $big_text_rect->hbarPolicy = NEVER;
        $big_text_rect->vbarPolicy = NEVER;
        // $big_text_rect->style = '-fx-base:black;';
        $big_text_rect->style = '-fx-base:transparent;';

        $image_r = new UXImageArea(new UXImage($image));
        $image_r->size = [230, 107];
        $image_r->position = [0, 0];
        $image_r->stretch = 1;
        $blur = new GaussianBlurEffectBehaviour();
        $blur->radius = 14.6;// change
        $blur->apply($image_r);

        $text_name_r = new UXLabel($name);
        $text_name_r->font->family = 'Franklin Gothic Medium';// change
        $text_name_r->font->size = '15';// change
        $text_name_r->textColor = white;
        // $text_name_r->backgroundColor = UXColor::rgb(0, 0, 0, 0.2);// change
        $text_name_r->size = [224, 20];
        $text_name_r->position = [6, 1];

        $text_data_r = new UXLabel($data);
        $text_data_r->font->family = 'Franklin Gothic Medium';// change
        $text_data_r->font->size = '14';// change
        $text_data_r->textColor = white;
        $text_data_r->alignment = BASELINE_RIGHT;
        // $text_data_r->backgroundColor = UXColor::rgb(0, 0, 0, 0.2);// change
        $text_data_r->size = [85, 15];
        $text_data_r->position = [140, 65];

        $text_tag_r = new UXLabel($tag);
        $text_tag_r->font->family = 'Franklin Gothic Medium';// change
        $text_tag_r->font->size = '14';// change
        $text_tag_r->textColor = white;
        $text_tag_r->alignment = TOP_LEFT;
        // $text_tag_r->backgroundColor = UXColor::rgb(0, 0, 0, 0.2);// change
        $text_tag_r->size = [115, 20];
        $text_tag_r->position = [6, 85];

        $context_name_r = new UXLabel($context);
        $context_name_r->font->family = 'Franklin Gothic Medium';// change
        $context_name_r->font->size = '12';// change
        $context_name_r->textColor = white;
        $context_name_r->alignment = TOP_LEFT;
        // $context_name_r->backgroundColor = UXColor::rgb(0, 0, 0, 0.2);// change
        $context_name_r->size = [224, 46];
        $context_name_r->position = [6, 20];
        $context_name_r->wrapText = 1;

        $text_amout_r = new UXLabel('Версия: ' . $version);
        $text_amout_r->font->family = 'Franklin Gothic Medium';// change
        $text_amout_r->font->size = '14';// change
        $text_amout_r->textColor = white;
        $text_amout_r->alignment = BASELINE_RIGHT;
        //$text_amout_r->backgroundColor = UXColor::rgb(0, 0, 0, 0.2);// change
        //$text_amout_r->style = '-fx-font-family: Franklin Gothic Medium;-fx-font-size: 14.0;-fx-font-weight: normal;-fx-font-style: normal;';
        $text_amout_r->size = [105, 20];
        $text_amout_r->position = [120, 79];

        //$reiting_panel = new UXPanel();
        //$reiting_panel->borderWidth = 0;
        //$reiting_panel->position = [6, 68];
        // $reiting_panel->backgroundColor = UXColor::rgb(0, 0, 0, 0.2);// change
        //for ($i = 0; $i < 5; $i++) {
        //    $panel = new UXPanel();
        //    // $rect->fillColor = '#99b3ff';
        //    $panel->borderColor = black;// change
        //    $panel->size = [15, 15];
        //    $panel->style = '-fx-min-height:0;-fx-min-width:0;-fx-shape:"M0.000,41.000 L0.000,37.000 L36.000,31.000 L51.000,-0.000 L55.000,-0.000 L71.000,31.000 L106.000,37.000 L106.000,41.000 L81.000,64.000 L87.000,96.000 L87.000,100.000 L86.000,101.000 L82.000,101.000 L54.000,85.000 L52.000,85.000 L24.000,101.000 L20.000,101.000 L19.000,100.000 L24.000,65.000 L0.000,41.000 Z";';
        //    if ($i < $reiting) {
        //        $panel->backgroundColor = '#ffff4d';// change
        //    } else {
        //        $panel->backgroundColor = '#f2f2f2';// change
        //    }
        //    $panel->position = [16 * $i, 0];
        //    $reiting_panel->add($panel);

        //}

        $cont_text_rect = new UXPanel();
        //$cont_text_rect->children->addAll([$image_r, $text_name_r, $text_data_r, $context_name_r, $text_tag_r]);
        $cont_text_rect->children->addAll([$image_r, $text_amout_r, $text_name_r, $text_data_r, $context_name_r, $text_tag_r]);

        $big_text_rect->content = $cont_text_rect;
        $big_text_rect->mouseTransparent = 1;

        $scroll_hover_panel = new UXScrollPane();
        $scroll_hover_panel->size = [230, 107];
        $scroll_hover_panel->position = [0, 0];
        $scroll_hover_panel->hbarPolicy = $scroll_hover_panel->vbarPolicy = NEVER;
        $scroll_hover_panel->style = '-fx-base:transparent;';

        $scroll_hover_panel->on('click', function (UXMouseEvent $click) use ($good) {// действие при клике на панель
            if ($click->button == PRIMARY) {
                pre($good->data('data'));
            }

        })

        $hover_panel = new UXAnchorPane();
        $hover_panel->add($big_text_rect);
        $scroll_hover_panel->content = $hover_panel;
        $big_text_rect->y = 107;
        $big_text_rect->opacity = 0;
        // $hover_panel->add($big_text_rect);

        $scroll_hover_panel->on('mouseExit', function () use ($scroll_hover_panel, $big_text_rect) {
            $timer = $scroll_hover_panel->data('timer');
            if (isset($timer)) {
                $timer->stop();
                $timer->on('action', function () use ($timer, $big_text_rect) {
                    $big_text_rect->y += 5;// change
                    $big_text_rect->opacity -= 0.1;// change
                    if ($big_text_rect->y > 108) {// change
                        $big_text_rect->y = 107;// change
                        $big_text_rect->opacity = 0;
                        $timer->stop();
                    }
                });
                $timer->start();
            } else {
                $timer = new TimerScript;
                $scroll_hover_panel->data('timer', $timer);
                $timer->interval = 10;
                $timer->repeatable = 1;
                $timer->on('action', function () use ($timer, $big_text_rect) {
                    $big_text_rect->y += 5;// change
                    $big_text_rect->opacity -= 0.1;// change
                    if ($big_text_rect->y > 108) {// change
                        $big_text_rect->y = 107;// change
                        $big_text_rect->opacity = 0;
                        $timer->stop();
                    }
                });
                $timer->start();

            }
        });

        $scroll_hover_panel->on('mouseEnter', function () use ($scroll_hover_panel, $big_text_rect) {
            $timer = $scroll_hover_panel->data('timer');
            if (isset($timer)) {
                $timer->stop();
                $timer->on('action', function () use ($timer, $big_text_rect) {
                    $big_text_rect->y -= 5;// change
                    $big_text_rect->opacity += 0.1;// change
                    if ($big_text_rect->y < -1) {// change
                        $big_text_rect->y = 0;// change
                        $big_text_rect->opacity = 1;
                        $timer->stop();
                    }
                });
                $timer->start();
            } else {
                $timer = new TimerScript;
                $scroll_hover_panel->data('timer', $timer);
                $timer->interval = 10;
                $timer->repeatable = 1;
                $timer->on('action', function () use ($timer, $big_text_rect) {
                    $big_text_rect->y -= 5;// change
                    $big_text_rect->opacity += 0.1;// change
                    if ($big_text_rect->y < -1) {// change
                        $big_text_rect->y = 0;// change
                        $big_text_rect->opacity = 1;
                        $timer->stop();
                    }
                });
                $timer->start();

            }
        });

    // ----------------------------------------
    $good->children->addAll([$imagea, $text_rect, $scroll_hover_panel]);

    $dropShadowEffect = new DropShadowEffectBehaviour();
    $dropShadowEffect->color = '#999999';// change
    $dropShadowEffect->apply($good);
    $dropShadowEffect->radius = 4.88;// change
    $dropShadowEffect->offsetX = $dropShadowEffect->offsetY = 5.54;// change

    $good->data('data', ['amout' => $amout, 'name' => $name, 'rei' => $reiting, 'context' => $context, 'date' => $data, 'tags' => $tag]);

    return $good;

    }

    /**
     * @return UXLabel
     */
    function scroll_Label($textt, $width = 54, $maxWidth = 49, $r = 189, $g = 206, $b = 255, $op = 1, $prefix = null, $tooltip = false)
    {
        $text = new UXLabel($textt);
        $text->data('text', $text->text);
        $text->data('pos', 0);
        $text->on('mouseEnter', function () use ($text, $maxWidth, $width, $r, $g, $b, $op) {
            $text->backgroundColor = UXColor::rgb($r, $g, $b, $op);
        });

        $text->on('mouseExit', function () use ($text, $maxWidth, $width, $r, $g, $b, $op) {
            $text->backgroundColor = UXColor::rgb($r, $g, $b, 0);
        });

        for ($i = 0; $i < $width; $i++) {
            $text_array = explode('', $text->data('text'));

            $res .= $text_array[$i];
        }
        $text->text = $prefix . $res;

        if ($tooltip) {
            $text->tooltipText = $prefix . $text->data('text');
        }

        $text->on('scroll', function (UXScrollEvent $r) use ($text, $prefix, $maxWidth, $width) {

            if ($r->textDeltaY == -3) {
                // $text->backgroundColor = UXColor::rgb(255, 255, 0, 1);
                if ($text->data('pos') + 1 > 1) {
                    $text->data('pos', $text->data('pos') - 1);
                }
            } else {
                if ($text->data('pos') < strlen($text->data('text')) - $maxWidth) {
                    $text->data('pos', $text->data('pos') + 1);
                }
            }

            for ($i = $text->data('pos'); $i < $width + $text->data('pos'); $i++) {
                $text_array = explode('', $text->data('text'));

                $res .= $text_array[$i];
            }

            $text->text = $prefix . $res;
            // var_dump($text->data('pos'));
        });
        return $text;
    }

    /**
     * @event panel4.mouseEnter
     */
    function doPanel4MouseEnter(UXMouseEvent $e = null)
    {
        $timer = $e->sender->data('timer');
        if (isset($timer)) {
            $timer->stop();
            $timer->on('action', function (ScriptEvent $t) use ($timer, $e, $this) {
                $this->panel5->y -= 7;
                $this->panel5->opacity += 0.1;
                if ($this->panel5->y < -1) {
                    $this->panel5->y = 0;
                    $this->panel5->opacity = 1;
                    $timer->stop();
                }
            });
            $timer->start();
        } else {
            $timer = new TimerScript;
            $e->sender->data('timer', $timer);
            $timer->interval = 10;
            $timer->repeatable = 1;
            $timer->on('action', function (ScriptEvent $t) use ($timer, $e, $this) {
                $this->panel5->y -= 7;
                $this->panel5->opacity += 0.1;
                if ($this->panel5->y < -1) {
                    $this->panel5->y = 0;
                    $this->panel5->opacity = 1;
                    $timer->stop();
                }
            });
            $timer->start();

        }
    }

    /**
     * @event panel4.mouseExit
     */
    function doPanel4MouseExit(UXMouseEvent $e = null)
    {
        $timer = $e->sender->data('timer');
        if (isset($timer)) {
            $timer->stop();
            $timer->on('action', function () use ($timer, $e, $this) {
                $this->panel5->y += 5;
                $this->panel5->opacity -= 0.1;
                if ($this->panel5->y > 108) {
                    $this->panel5->y = 107;
                    $this->panel5->opacity = 0;
                    $timer->stop();
                }
            });
            $timer->start();
        } else {
            $timer = new TimerScript;
            $e->sender->data('timer', $timer);
            $timer->interval = 10;
            $timer->repeatable = 1;
            $timer->on('action', function () use ($timer, $e, $this) {
                $this->panel5->y += 5;
                $this->panel5->opacity -= 0.1;
                if ($this->panel5->y > 108) {
                    $this->panel5->y = 107;
                    $this->panel5->opacity = 0;
                    $timer->stop();
                }
            });
            $timer->start();

        }
    }

    /**
     * @event button.action
     */
    function doButtonAction(UXEvent $e = null)
    {
        global $arr, $data;
        $arr[] = $this->add_goods($this->edit->text, $this->textArea->text, $this->numberFieldAlt->value, $this->dateEdit->value, $this->editAlt->text, $this->numberField->value, $this->label->text);
        $this->tilePaneAlt->children->clear();
        $this->tilePaneAlt->children->addAll($arr);

        $data[] = ['amout' => $this->numberFieldAlt->value, 'name' => $this->edit->text, 'rei' => $this->numberField->value, 'context' => $this->textArea->text, 'date' => $this->dateEdit->value, 'tags' => $this->editAlt->text, 'image' => $this->label->text];
        $this->textAreaAlt->text = serialize($data);
    }

    /**
     * @event combobox.step
     */
    function doComboboxStep(UXEvent $e = null)
    {
        if ($e->sender->selectedIndex == 0) {
            if ($e->sender->value != 'Названию') {
                $e->sender->value = 'Названию';
            }
        }

        switch ($e->sender->selectedIndex) {
            case 1:
                $this->type = 'version';
                break;

            case 2:
                $this->type = 'date';
                break;


            default:
                $this->type = 'name';
                break;
        }

// $this->sort_and_show($this->result, $this->type); 
    }

    /**
     * @event combobox.action
     */
    function doComboboxAction(UXEvent $e = null)
    {
        switch ($e->sender->selectedIndex) {
            case 1:
                $this->type = 'version';
                break;

            case 2:
                $this->type = 'date';
                break;

            default:
                $this->type = 'name';
                break;
        }
        $this->search_button();
    }

    /**
     * @event button4.action
     */
    function doButton4Action(UXEvent $e = null)
    {
        $this->edit3->text = '';
        $this->search_button();
    }

    /**
     * @event image5.construct
     */
    function doImage5Construct(UXEvent $e = null)
    {
        $e->sender->mouseTransparent = 1;
    }


    /**
     * @event button6.action
     */
    function doButton6Action(UXEvent $e = null)
    {
        file_put_contents('settings.txt', $this->numberField3->value . '|' . $this->numberField4->value);
        $this->doConstruct();
        $this->panel6->visible = 0;
    }

    /**
     * @event button7.action
     */
    function doButton7Action(UXEvent $e = null)
    {
        $this->panel6->visible = 0;
    }

    /**
     * @event link.action
     * @event linkAlt.action
     * @event link3.action
     */
    function doLinkAction(UXEvent $e = null)
    {
        $search = $e->sender->text;
        $type = 'tags';

        $this->search_and_sort($search, $type, 1);
    }


    /**
     * @event reverse.click-Left
     */
    function doReverseClickLeft(UXMouseEvent $e = null)
    {
        $this->search_button();
    }

    /**
     * @event button5.action 
     */
    function doButton5Action(UXEvent $e = null)
    {
        $this->panel6->visible = 1;
        
        $this->panel6->x = 0;
    }

    /**
     * @event left_but.mouseDown-Left 
     */
    function doRight_butMouseDownLeft(UXMouseEvent $e = null)
    {    
        $e->sender->data('press', 1);
        (new Thread(function () use ($e){
            
        
        start:
        wait(10);
        if ($e->sender->data('press')){
            
            if ($this->panel_tag_show->x <= 0){
                $this->panel_tag_show->x += 1;
                goto start;
            } 
        }
        }))->start();
    }

    /**
     * @event right_but.mouseUp-Left
     * @event left_but.mouseUp-Left 
     */
    function doRight_butMouseUpLeft(UXMouseEvent $e = null)
    {    
        $e->sender->data('press', 0);
    }

    /**
     * @event right_but.mouseDown-Left 
     */
    function doLeft_butMouseDownLeft(UXMouseEvent $e = null)
    {    
        $e->sender->data('press', 1);
        (new Thread(function () use ($e){
            
        
        start:
        wait(10);
        if ($e->sender->data('press')){
            
            if ($this->panel_tag_show->x >= $this->tag_panel->width - $this->panel_tag_show->width){
                $this->panel_tag_show->x -= 1;
                goto start;
            } 
        }
        }))->start();
    }




















}
