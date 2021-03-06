<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin extends Controller
{
    protected $_table_name = array();

    public function before()
    {
        parent::before();

        // мыло админа сайта
        $email = Session::instance()->get('email'); if(empty($email)) throw new HTTP_Exception_404();

        $this->auto_render = false;
        $post = $this->request->post();
        if (Kohana::$environment === Kohana::PRODUCTION)
            if (!Request::initial()->is_ajax() || empty($post))
                throw new HTTP_Exception_404();

        //так делать не хорошо, но кому не лень писать каждую новую таблицу в массив?
        $tables = Database::instance()->list_tables();

        // создание файлов моделей по списку таблиц в БД
        $this->_createAllModels($tables);
        foreach ($tables as $k => $v)
            $tables[$k] = UTF8::strtolower($v);

        $path = APPPATH.'classes/Model/';
        //список всех файлов в папке $path;
        $list = $this->_listFiles($path);

        // если список имен файлов не совпадает со списком таблиц, тогда удадить этот файл. (удалишь табл - удалится модель)
        if ($file = array_diff($list, $tables))
            foreach ($file as $val)
                if (file_exists($path.$val.EXT))
                    unlink($path.$val.EXT);
        // беру имя таблицы из ссылки
        $this->_table_name = $this->request->param('table_name');
        // проверка есть ли такая таблица в бд
        if (!empty($this->_table_name))
            if (!in_array($this->_table_name, $tables, true)) {
                $this->ajax_msg('Такой таблицы не существует', 'error');
                exit;
            }

        // данные из ajax.js скрипта
        $data = $this->request->post('data');
        $noreq = $this->request->post('noreq');

        // список тех метов, которые не нужно вызвать для проверки post данные на xss
        $no_check = array(
            'read',
            'delete'
        );

        if (!in_array($this->request->action(), $no_check)) {
            //  очистка всего полчученного поста от ajax.js от мусора xss
            $data_without_noreq = array();
            $noreq_clean = array();

            foreach ($data as $k => $v)
            {
                if (in_array($k, $noreq)) {
                    $noreq_clean[] = Security::xss_clean($data[$k]);
                    continue;
                }
                $data_without_noreq[$k] = $v;
            }

            if (!empty($data_without_noreq))
            {
                $this->ajax_xssclean($data_without_noreq, 'Нет данных, введите их пожалуйста');
                exit;
            }

        }
    }

    /**
     * создание, insert
     * site.ru/admin/table_name/create
    post => [
    'field' => 'val',
    'field2' => 'val2',
    ...
    ]
     *
     * а тута поле коротое будет указано (если оно есть в табл), будет проверяться по содержимому,
     * если такое есть - ошибка, если нету такого поля - добивит данные в бд
     * site.ru/admin/table_name/create|check-field
    post => [
    'field' => 'val',
    'field2' => 'val2',
    ...
    ]
     */
    public function action_create()
    {
        $params = $this->request->param('params');
        $return_add_data = $this->request->query('return');
        if (!empty($params)) {
            $params = explode('-', $params);
            if ($params[0] === 'check' && !empty($params[1])) {
                $columns = Database::instance()->list_columns($this->_table_name);
                if (!in_array($params[1], array_keys($columns), true)) {
                    $this->ajax_msg($params[1].'Такого поля не существует', 'error');
                    exit;
                }
                if (!empty($this->request->post('data')[$params[1]])) {
                    $result = Model::factory($this->_table_name)
                        ->getBy($params[1], $this->request->post('data')[$params[1]]);
                    if (!$result) {
                        $resultAdd = Model::factory($this->_table_name)
                            ->addRec($this->request->post('data'));

                        if ($resultAdd && empty($return_add_data)) {
                            $this->ajax_msg('Добавлено');
                            exit;
                        } elseif ($return_add_data === 'true') {
                            $data = array(
                                'id' => $resultAdd,
                                'email' => $this->request->post('data.email')
                            );

                            $this->ajax_data($data, 'Добавлено');
                            exit;
                        } else {
                            $this->ajax_msg('Ошибка добавления', 'error');
                            exit;
                        }
                    } else {
                        $this->ajax_msg('Такие данные уже есть', 'error');
                        exit;
                    }
                }

            }
        } else {
            $result = Model::factory($this->_table_name)
                ->addRec($this->request->post('data'));

            if ($result) {
                $this->ajax_msg('Добавлено');
                exit;
            } else {
                $this->ajax_msg('Ошибка добавления', 'error');
                exit;
            }
        }

    }

    /**
     * выборка
     * site.ru/admin/table_name/read/12?sort=asc
     * site.ru/admin/table_name/read/12
     * site.ru/admin/table_name/read
     * или выборка по типу поле = значение
     * site.ru/admin/table_name/read|field-value?sort=desc
     * site.ru/admin/table_name/read|field-value
     */
    public function action_read()
    {
        $id = $this->request->param('id');
        $params = $this->request->param('params');
        $sort = $this->request->query('sort');

        if (null === $id) {

            $result = Model::factory($this->_table_name)
                ->all(
                    $sort === 'asc' || $sort === 'desc' ? $sort : 'asc'
                );

            if ($result) {
                $data = array();
                foreach ($result as $val)
                    $data[] = $val->as_array();

                $this->ajax_data($data);
                exit;
            } else {
                $this->ajax_msg('Ошибка чтения', 'error');
                exit;
            }

        } elseif (!empty($params)) {
            $arr = explode('-', $params);
            if (count($arr) !== 2 ) { //|| count($this->request->post('data')) !== 2
                $this->ajax_msg('Ошибка чтения записей', 'error');
                exit;
            }

            $result = Model::factory($this->_table_name)
                ->allWhere(
                    $arr[0],
                    $arr[1],
                    $sort === 'asc' || $sort === 'desc' ? $sort : 'asc'
                );

            if ($result) {

                $data = array();
                foreach ($result as $value)
                    $data[] = $value->as_array();

                $this->ajax_data($data);
                exit;
            } else {
                $this->ajax_msg('Ошибка выборки', 'error');

                exit;
            }
        } else {
            $result = Model::factory($this->_table_name)
                ->getBy('id', $id);

            if ($result) {
                $this->ajax_data($result->as_array());
                exit;
            } else {
                $this->ajax_msg('Ошибка чтения', 'error');
                exit;
            }
        }
    }

    /**
     * изменение
     * site.ru/admin/table_name/update/12
    post => [
    'field' => 'val',
    ...
    ]
     * или изменить значение какоголибо поля где field = value
     * site.ru/admin/table_name/update|field-value
    post => [
    'field',
    'val'
    ]
     */
    public function action_update()
    {
        $id = $this->request->param('id');
        $params = $this->request->param('params');
        if (null === $id) {
            $this->ajax_msg('Ошибка обновления записей', 'error');
            exit;
        } elseif (!empty($params)) {

            $arr = explode('-', $params);
            if (count($arr) !== 2 ) { //|| count($this->request->post('data')) !== 2
                $this->ajax_msg('Ошибка обновления записей', 'error');
                exit;
            }

            $result = Model::factory($this->_table_name)
                ->upd($arr, $this->request->post('data'));

            if ($result) {
                $this->ajax_msg('Данные обновлены');
                exit;
            } else {
                $this->ajax_msg('Ошибка обновления', 'error');
                exit;
            }
        } else {

            $result = Model::factory($this->_table_name)
                ->upd($id, $this->request->post('data'));

            if ($result) {
                $this->ajax_msg('Данные обновлены');
                exit;
            } else {
                $this->ajax_msg('Ошибка обновления', 'error');
                exit;
            }
        }


    }

    /**
     * удаление
     */
    public function action_delete()
    {
        $id = $this->request->param('id');
        $return_add_data = $this->request->query('return');
        if (null === $id) {
            $this->ajax_msg('Ошибка удаления записи', 'error');
            exit;
        } else {
            $result = Model::factory($this->_table_name)
                ->del($id);

            if ($result && empty($return_add_data)) {
                $this->ajax_msg('Удалено');
                exit;
            } elseif ($return_add_data === 'true') {
                $data = array(
                    'id' => $id,
                );
                $this->ajax_data($data, 'Удалено');
                exit;

            } else {
                $this->ajax_msg('Ошибка удаления', 'error');
                exit;
            }
        }
    }

    /**
     * генерация данных в файл модели
     * всякий мусор в $_table_columns необходм, чтобы не делать каждый раз запрос SHOW FULL COLUMNS
     * @param $tables
     */
    protected function _createAllModels($tables)
    {
        if (empty($tables)) {
            $this->ajax_msg('таблицы в бд не созданы', 'error');
            exit;
        }

        foreach ($tables as $item) {
            $columns = Database::instance()->list_columns($item);

            $modelName = UTF8::ucwords(UTF8::strtolower($item));
            $file = APPPATH.'classes/Model/'.$modelName.EXT;
/*            echo 'ok - '.$item.'<br>';
            if ($lol = preg_match('/protected \$_table_columns \= array\(/', file_get_contents($file))) {
                echo 'NUUUU';
                echo '<pre>';
                print_r($lol);
                echo '</pre>';
            }*/

            if (!file_exists($file)) {
                foreach ($columns as $column) {
                    if ($column['key'] === 'PRI')
                        $primary_key = $column['column_name'];
                }

                $content = "<?php defined('SYSPATH') or die('No direct access allowed.');
\nclass Model_". $modelName ." extends ORM
{
	protected \$_db = 'default';
    protected \$_table_name  = '".$modelName."';
    protected \$_primary_key = '$primary_key';

    protected \$_table_columns = array(\n";
                foreach ($columns as $column)
                    $content .= "\t\t'". $column['column_name'] ."' => array('data_type' => '". $column['type'] ."', 'is_nullable' => ". ( ( $column['is_nullable'] ) ? "true" : "false" ) ."),\n";

                $content .= "\t);";
                $content .= "\n}";

                file_put_contents($file, $content);
            }

        }
    }

    /**
     * Файлы без php расширения
     * @param $dir
     *
     * @return array
     */
    public function _listFiles($dir)
    {
        $data = array();
        try {
            $it = new DirectoryIterator($dir);
        } catch (Exception $e) {
            // короче директория не найдена
            Kohana_Exception::handler($e);
        }

        foreach ($it as $file)
            if (!$it->isDot() && !$it->isDir())
                $data[] = UTF8::strtolower($it->getBasename('.php'));

        return $data;
    }


}