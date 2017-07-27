<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

/**
 * CRUD MODEL RELATION WITH DATABASE
 * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
 */
class Crud_Model {

    // Database where clause
    /**
     * @var
     */
    private $display_deleted;
    /**
     * @var
     */
    private $where;
    /**
     * @var
     */
    private $orderby;
    /**
     * @var
     */
    private $groupby;
    /**
     * @var
     */
    private $like;
    /**
     * @var
     */
    private $limit;
    /**
     * @var
     */
    private $relation;
    /**
     * @var
     */
    private $relation_n_n;
    /**
     * @var
     */
    private $table_name;
    /**
     * @var
     */
    private $change_field_type;
    /**
     * @var
     */
    private $change_field_class;
    /**
     * @var
     */
    private $required;

    /**
     * @var
     */
    private $validation;

    /**
     * This method used to display deleted rows
     * @name       display_deleted
     * @param bool $display
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    public function display_deleted($display = FALSE) {
        $this->display_deleted = $display;
    }

    /**
     *
     * @param string $field
     * @param string $type
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function change_field_type($field, $type) {
        $this->change_field_type[$field] = $type;
    }

    /**
     * @param string $field
     * @param string $class
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function change_field_class($field, $class) {
        $this->change_field_class[$field] = $class;
    }

    /**
     * Get table name used on initialization of crud
     * @return string
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function getTable_name() {
        return $this->table_name;
    }

    /**
     * Used to set a description for Table
     * @param string $table_name
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function setTable_name($table_name) {
        $this->table_name = $table_name;
    }

    /**
     * Used to get Primary Key of given table
     * @example {'id' => 1}
     * @param type $table
     * @return object
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function get_primary_key($table) {
        return DB::select(DB::raw("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'")) [0]->Column_name;
    }

    /**
     * Return Columns Names
     * @param string $table
     * @return array
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function get_columns_names($table) {
        $column_names = [];
        $columns = Schema::getColumnListing($table);

        foreach ($columns as $index => $value) {
            array_push($column_names, $value);
        }

        return $column_names;
    }

    /**
     * @param type $table
     * @param type $columns
     * @return type
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function get_columns_type($table, $columns) {
        $types = [];

        $results = DB::select(DB::raw("SHOW COLUMNS FROM `$table`"));

        foreach ($results as $result) {
            if (in_array($result->Field, $columns)) {
                $types[$result->Field] = $result->Type;
            } else {
                continue;
            }

            //$types[$column] = DB::connection()->getDoctrineColumn($table, $column)->getType()->getName();
        }

        if (!empty($this->relation)) {
            if (is_array($this->relation)) {
                foreach ($this->relation as $relation) {
                    list($field_name, $related_table, $related_field_title, $related_field_where) = $relation;
                    if (!is_array($related_field_title)) {
                        $types[$field_name] = "relation($related_table,$related_field_title, $related_field_where)";
                    } else {
                        // Get only first column
                        $display_field = implode('|', $related_field_title);//[0];
                        $types[$field_name] = "relation($related_table,$display_field,$related_field_where)";
                    }
                }
            }
        }

        if (!empty($this->change_field_type)) {
            foreach ($this->change_field_type as $key => $value) {
                $types[$key] = $value;
            }
        }

        //Extra Columns: Added Manually
        foreach ($columns as $column) {
            if (!isset($types[$column])) {
                $types[$column] = 'varchar(50)';
            }
        }

        return $types;
    }

    /**
     *
     * @param string $table
     * @param string $columns
     * @return string
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function get_columns_class($table, $columns) {
        $class = [];
        $results = DB::select(DB::raw("SHOW COLUMNS FROM `$table`"));

        foreach ($results as $result) {
            if (in_array($result->Field, $columns)) {
                $class[$result->Field] = 'col-sm-3';
                //$types[$column] = DB::connection()->getDoctrineColumn($table, $column)->getType()->getName();
            } else {
                continue;
            }
        }

        if (!empty($this->change_field_class)) {
            foreach ($this->change_field_class as $key => $value) {
                $class[$key] = $value;
            }
        }

        //Extra Columns: Added Manually
        foreach ($columns as $column) {
            if (!isset($class[$column])) {
                $class[$column] = 'col-sm-3';
            }
        }

        return $class;
    }

    /**
     *
     * @param string $table
     * @param string $columns
     * @return string
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    function get_columns_validation($table, $columns) {
        $validation = [];

        if (!empty($this->validation)) {
            $results = DB::select(DB::raw("SHOW COLUMNS FROM `$table`"));

            foreach ($results as $result) {
                if (in_array($result->Field, $columns)) {
                    if (isset($this->validation[$result->Field])) {
                        $validation[$result->Field] = $this->validation[$result->Field];
                    }
                }
            }
        }

        return $validation;
    }

    /**
     * @param $table
     * @param $columns
     * @author Eledi Dyrkaj
     * @return array
     */
    public function get_columns_required($table, $columns) {
        $req = [];
        $results = DB::select(DB::raw("SHOW COLUMNS FROM `$table`"));

        foreach ($results as $result) {
            if (in_array($result->Field, $columns)) {
                $required = $result->Field;
                if (!empty($this->required) && in_array($required, $this->required)) {
                    $req[$required] = TRUE;
                } else {
                    $req[$required] = FALSE;
                }
            } else {
                continue;
            }
        }

        //Extra Columns: Added Manually
        foreach ($columns as $column) {
            if (!isset($req[$column])) {
                if (!empty($this->required) && in_array($required, $this->required)) {
                    $req[$required] = TRUE;
                } else {
                    $req[$required] = FALSE;
                }
            }
        }

        return $req;
    }

    /**
     * @param $table
     * @author Eledi Dyrkaj
     * @return array
     */
    function get_columns_indexes($table) {
        $column_indexes = [];

        $columns = Schema::getColumnListing($table);

        foreach ($columns as $index => $value) {
            array_push($column_indexes, $index);
        }

        return $column_indexes;
    }

    /**
     * @param      $table
     * @param null $criteria
     * @author Eledi Dyrkaj
     * @return null
     */
    function get_columns_data($table, $criteria = null) {
        $data = null;

        if (isset($criteria)) {
            $results = DB::table($table)->select(DB::raw("*"))->where($criteria);

            if (Schema::hasColumn($this->table_name, 'deleted_at') && $this->display_deleted == FALSE) {
                $results->whereNull($this->table_name . '.deleted_at');
            }

            $data = $results->get();
        } else {
            $select = "`{$table}`.*";

            //set_relation special queries
            if (!empty($this->relation)) {
                foreach ($this->relation as $index_rel => $relation) {
                    list($field_name, $related_table, $related_field_title) = $relation;

                    if (is_array($related_field_title)) {
                        $concat = " CONCAT(";
                        $aConcat = [];

                        foreach ($related_field_title as $index => $element) {
                            if ($index % 2 == 0) {
                                array_push($aConcat, "`$related_table`.`$element`");
                            } else {
                                array_push($aConcat, "'$element'");
                            }
                        }

                        $concat .= implode(',', $aConcat);
                        $concat .= ") as rel_$index_rel";

                        $select .= ", " . $concat;
                    } else {
                        if ($this->field_exists($related_field_title, $related_table)) {
                            $select .= ", `$related_table`.`$related_field_title` as rel_$index_rel";
                        }
                    }
                }

                $results = DB::table($table)->select(DB::raw($select));

                if (Schema::hasColumn($this->table_name, 'deleted_at') && $this->display_deleted == FALSE) {
                    $results->whereNull($this->table_name . '.deleted_at');
                }

                foreach ($this->relation as $relation) {
                    list($field_name, $related_table, $related_field_title) = $relation;

                    $table_pk = $this->get_primary_key($table);
                    $related_pk = $this->get_primary_key($related_table);
                    $results->leftJoin($related_table, $table . '.' . $field_name, '=', $related_table . '.' . $related_pk);
                }

                // Manage Where Clause
                if (!empty($this->where)) {
                    foreach ($this->where as $column => $related_values) {
                        list($operator, $related_value) = $related_values;

                        if (strcmp(strtolower($operator), 'in') == 0) {
                            if (!is_array($related_value)) {
                                $related_value = [$related_value];
                            }
                            $results->whereIn($table . '.' . $column, $related_value);
                        } else if (strcmp(strtolower($operator), 'not_in') == 0) {
                            if (!is_array($related_value)) {
                                $related_value = [$related_value];
                            }
                            $results->whereNotIn($table . '.' . $column, $related_value);
                        } else if (strcmp(strtolower($operator), 'between') == 0) {
                            if (is_array($related_value) && !empty($related_value)) {
                                $results->whereBetween($table . '.' . $column, $related_value);
                            }
                        } else {
                            $results->where($table . '.' . $column, $operator, $related_value);
                        }
                    }
                }

                // Manage Like Clause
                if (!empty($this->like)) {
                    foreach ($this->like as $column => $related_value) {
                        $results->where($table . '.' . $column, 'LIKE', $related_value);
                    }
                }

                // Manage GroupBy
                if (!empty($this->groupby)) {
                    if (is_array($this->groupby)) {
                        foreach ($this->groupby as $column) {
                            $results->groupBy($column);
                        }
                    }
                }

                // Manage OrderBy
                if (!empty($this->orderby)) {
                    if (is_array($this->orderby)) {
                        foreach ($this->orderby as $column => $type) {
                            $results->orderBy($column, $type);
                        }
                    }
                }

                $data = $results->get();

                foreach ($data as $result) {
                    foreach ($this->relation as $index_rel => $relation) {
                        list($field_name, $related_table, $related_field_title) = $relation;
                        $rel_field = "rel_$index_rel";

                        $result->$field_name = $result->$rel_field;
                        unset($result->$rel_field);
                    }
                }
            } else {
                $results = DB::table($table)->select(DB::raw($select));

                if (Schema::hasColumn($this->table_name, 'deleted_at') && $this->display_deleted == FALSE) {
                    $results->whereNull($this->table_name . '.deleted_at');
                }

                // Manage Where Clause
                if (!empty($this->where)) {
                    foreach ($this->where as $column => $related_values) {
                        list($operator, $related_value) = $related_values;

                        if (strcmp(strtolower($operator), 'in') == 0) {
                            if (!is_array($related_value)) {
                                $related_value = [$related_value];
                            }
                            $results->whereIn($table . '.' . $column, $related_value);
                        } else if (strcmp(strtolower($operator), 'not_in') == 0) {
                            if (!is_array($related_value)) {
                                $related_value = [$related_value];
                            }
                            $results->whereNotIn($table . '.' . $column, $related_value);
                        } else if (strcmp(strtolower($operator), 'between') == 0) {
                            if (is_array($related_value) && !empty($related_value)) {
                                $results->whereBetween($table . '.' . $column, $related_value);
                            }
                        } else {
                            $results->where($table . '.' . $column, $operator, $related_value);
                        }
                    }
                }

                // Manage Like Clause
                if (!empty($this->like)) {
                    foreach ($this->like as $column => $related_value) {
                        $results->where($table . '.' . $column, 'LIKE', $related_value);
                    }
                }

                // Manage GroupBy
                if (!empty($this->groupby)) {
                    if (is_array($this->groupby)) {
                        foreach ($this->groupby as $column) {
                            $results->groupBy($column);
                        }
                    }
                }

                // Manage OrderBy
                if (!empty($this->orderby)) {
                    if (is_array($this->orderby)) {
                        foreach ($this->orderby as $column => $type) {
                            $results->orderBy($column, $type);
                        }
                    }
                }

                $data = $results->get();
            }
        }

        return $data;
    }

    /**
     * @param type $field
     * @param type $table_name
     * @return boolean
     * @author Eledi Dyrkaj
     */
    function field_exists($field, $table_name = null) {
        if (empty($table_name)) {
            $table_name = $this->table_name;
        }
        return Schema::hasColumn($table_name, $field);
    }

    /**
     *
     * Set a simple 1-n foreign key relation
     * @param string $field_name
     * @param string $related_table
     * @param string $related_title_field
     * @param mixed $where_clause
     * @param string $order_by
     * @author Eledi Dyrkaj
     * @return $this
     */
    public function set_relation($field_name, $related_table, $related_title_field, $where_clause = null, $order_by = null) {
        $this->relation[$field_name] = [
            $field_name,
            $related_table,
            $related_title_field,
            $where_clause,
            $order_by
        ];
        return $this;
    }

    /**
     * @param $fields_name
     * @author Eledi Dyrkaj
     */
    public function set_required($fields_name) {
        if (is_array($fields_name)) {
            $this->required = $fields_name;
        } else {
            $this->required = [$fields_name];
        }
    }


    /**
     * @param string $field
     * @param array $validation
     * @description This is used for jquery (javascript) validation form
     * First parameter is field name same as table column name
     * Second parameter is array with hash key same as used in jquery rules validation
     * Set required field must be set in crud initialization
     * @example
     *
     * $crud->set_field_validation('username', [min => 5]);
     *
     * @author Eledi Dyrkaj <Eledi Dyrkaj edyrkaj@gmail.com>
     */
    public function set_field_validation($field, $validation) {
        if (is_array($validation)) {
            $this->validation[$field] = htmlspecialchars(json_encode($validation));
        }
    }

    /**
     * @author Eledi Dyrkaj
     * @return mixed
     */
    public function get_relation() {
        return $this->relation;
    }

    /**
     * @param null $relation
     * @author Eledi Dyrkaj
     */
    public function relation_n_n($relation = null) {
        $this->relation_n_n = $relation;
    }

    /**
     * @param $field_name
     * @author Eledi Dyrkaj
     * @return string
     * not used in this version
     */
    protected function _unique_join_name($field_name) {
        return 'j' . substr(md5($field_name), 0, 8);

        //This j is because is better for a string to begin with a letter and not with a number
    }

    /**
     * @param $field_name
     * @author Eledi Dyrkaj
     * @return string
     * not used in this version
     */
    protected function _unique_field_name($field_name) {
        return 's' . substr(md5($field_name), 0, 8);

        //This character s is used for column alias to begin with a letter and not with a number
    }

    /**
     * @param $column
     * @param $operator
     * @param $related_value
     * @author Eledi Dyrkaj
     */
    public function where($column, $operator, $related_value) {
        $this->where[$column] = [
            $operator,
            $related_value
        ];
    }

    /**
     * @author Eledi Dyrkaj
     * @return mixed
     */
    public function get_where() {
        return $this->where;
    }

    /**
     *
     * @param string $column
     * @author Eledi Dyrkaj
     */
    public function groupBy($column) {
        $this->groupby[] = $column;
    }

    /**
     * @param        $column
     * @param string $type
     * @author Eledi Dyrkaj
     */
    public function orderBy($column, $type = 'asc') {
        $this->orderby[$column] = $type;
    }

    /**
     * @param $column
     * @param $related_value
     * @author Eledi Dyrkaj
     */
    public function like($column, $related_value) {
        $this->like[$column] = $related_value;
    }

    /**
     * @param null $limit
     * @author Eledi Dyrkaj
     */
    public function limit($limit = null) {
        $this->limit = $limit;
    }

}


/**
 * Class Crud_Layout
 */
class Crud_Layout extends Crud_Model {

    /**
     *
     * @param type $data
     * @return type
     */
    function get_add_form($data) {
        $actions = new stdClass();
        $actions->back = [
            'title' => trans('crud.back'),
            'url'   => URL::previous()
        ];
        $data['menu'] = $actions;

        if (Request::ajax()) {
            return view('subviews/crud/add', $data);
        } else {
            return view('crud.add', $data);
        }
    }

    /**
     *
     * @param type $data
     * @return type
     */
    function get_delete_form($data) {
        $actions = new stdClass();
        $actions->back = [
            'title' => trans('crud.back'),
            'url'   => URL::previous()
        ];

        $data['menu'] = $actions;

        return view('crud.delete', $data);
    }

    /**
     *
     * @param array $data
     * @return type
     */
    function get_edit_form($data) {
        $actions = new stdClass();
        $actions->back = [
            'title' => trans('crud.back'),
            'url'   => URL::previous()
        ];
        $data['menu'] = $actions;

        if (isset($this->callback_edit_field)) {
            foreach ($this->callback_edit_field as $key => $field) {
                if (isset($data['rows'][0]->$key)) {
                    $data['rows'][0]->$key = call_user_func($this->callback_edit_field[$key], $data['rows'][0]->$key, $data['rows'][0]->{$data['primary_key']});
                }
            }
        }

        if (!empty($this->callback_column)) {
            foreach ($data['columns'] as $column) {
                foreach ($data['rows'] as $index => $row) {
                    if (isset($this->callback_column[$column])) {
                        if (Schema::hasColumn($this->getTable_name(), $column)) {
                            $row->$column = call_user_func($this->callback_column[$column], $row->$column, $row);
                        } else {
                            $newColumn = new stdClass();
                            $newColumn->$column = call_user_func($this->callback_column[$column], null, $row);
                            $new_row = (object)array_merge((array)$row, (array)$newColumn);
                            $data['rows'][$index] = $new_row;
                        }
                    }

                }
            }
        }

        // Add custom script if isset
        if (isset($this->edit_script)) {
            $data['scripts'] = $this->edit_script;
        }

        if (Request::ajax()) {
            return view('subviews/crud/edit', $data);
        } else {
            return view('crud.edit', $data);
        }
    }

    /**
     *
     * @param type $data
     * @return type
     */
    function get_read_form($data) {
        $actions = new stdClass();
        $actions->back = [
            'title' => trans('crud.back'),
            'url'   => URL::previous()
        ];
        $data['menu'] = $actions;

        if (!empty($this->callback_column)) {
            foreach ($data['columns'] as $column) {
                foreach ($data['rows'] as $index => $row) {
                    if (isset($this->callback_column[$column])) {
                        if (Schema::hasColumn($this->getTable_name(), $column)) {
                            $row->$column = call_user_func($this->callback_column[$column], $row->$column, $row);
                        } else {
                            $newColumn = new stdClass();
                            $newColumn->$column = call_user_func($this->callback_column[$column], null, $row);
                            $new_row = (object)array_merge((array)$row, (array)$newColumn);
                            $data['rows'][$index] = $new_row;
                        }
                    }

                }
            }
        }

        // if is defined callback only for read form
        if (isset($this->callback_read_field)) {
            foreach ($this->callback_read_field as $key => $field) {
                if (isset($data['rows'][0]->$key)) {
                    $data['rows'][0]->$key = call_user_func($this->callback_read_field[$key], $data['rows'][0]->$key, $data['rows'][0]->{$data['primary_key']});
                }
            }
        }

        // Add custom script if isset
        if (isset($this->read_script)) {
            $data['scripts'] = $this->read_script;
        }

        if (Request::ajax()) {
            return view('subviews/crud/read', $data);
        } else {
            return view('crud.read', $data);
        }
    }

    /**
     * @param object $data
     * @return object
     */
    function get_show_form($data) {
        $actions = new stdClass();
        $actions->add = [
            'title' => trans('crud.add'),
            'url'   => Request::url() . '/add'
        ];

        $data['menu'] = $actions;

        // Display Row
        if (!empty($this->callback_column)) {
            foreach ($data['columns'] as $column) {
                foreach ($data['rows'] as $index => $row) {
                    if (isset($this->callback_column[$column])) {
                        if (Schema::hasColumn($this->getTable_name(), $column)) {
                            $row->$column = call_user_func($this->callback_column[$column], $row->$column, $row);
                        } else {
                            $newColumn = new stdClass();
                            $newColumn->$column = call_user_func($this->callback_column[$column], null, $row);
                            $new_row = (object)array_merge((array)$row, (array)$newColumn);
                            $data['rows'][$index] = $new_row;
                        }
                    }

                }
            }
        }

        // Add custom script if isset
        if (isset($this->show_script)) {
            $data['scripts'] = $this->show_script;
        }

        return view('crud.show')->with($data);
    }

}

/**
 * Class Crud
 */
class Crud extends Crud_Layout {
    //Constants used to get | set status of crud operation

    const ADD = "add";
    const SHOW = "show";
    const EDIT = "edit";
    const DELETE = "delete";
    const READ = "read";
    const RESTORE = "restore";
    const ACTIVATE = "activate";
    const INACTIVATE = "inactivate";
    const LOCK = "lock";
    const UNLOCK = "unlock";

    private $ajax_enabled = false;

    /**
     * @return boolean
     */
    public function isAjaxEnabled() {
        return $this->ajax_enabled;
    }

    /**
     * @param $ajax_enabled
     * @author Eledi Dyrkaj
     */
    public function setAjaxEnabled($ajax_enabled) {
        $this->ajax_enabled = $ajax_enabled;
    }

    /**
     * @var bool
     */
    private $table_exist = FALSE;
    /**
     * @var null
     */
    private $table_name;
    /**
     * @var null
     */
    private $table_title;
    /**
     * @var array
     */
    private $table_data;
    /**
     * @var object
     */
    private $primary_key;
    /**
     * @var string
     */
    private $layout = 'layouts.crud';
    /**
     * @var
     */
    private $columns;
    /**
     * @var array
     */
    private $unset_columns = [];
    /**
     * @var array
     */
    private $unset_add_columns = [];
    /**
     * @var array
     */
    private $unset_edit_columns = [];
    /**
     * @var array
     */
    private $unset_read_columns = [];
    /**
     * @var
     */
    private $unset_actions;
    /**
     * @var
     */
    private $unset_action;
    /**
     * @var
     */
    private $unset_title_action;
    /**
     * @var
     */
    private $unset_title_actions;
    /**
     * @var
     */
    private $display_as;
    /**
     * @var null
     */
    protected $show_script = null;
    /**
     * @var null
     */
    protected $add_script = null;
    /**
     * @var null
     */
    protected $edit_script = null;
    /**
     * @var null
     */
    protected $read_script = null;

    /**
     * @var array
     */
    private $actions;
    /**
     * @var array
     */
    private $title_actions;

    private $action_add;
    private $action_read;
    private $action_edit;
    private $action_delete;
    private $action_restore;
    private $action_activate;
    private $action_inactivate;
    private $action_lock;
    private $action_unlock;

    /**
     * @var
     */
    private $state;
    /**
     * @var
     */
    private $url;

    /* Callbacks */
    /**
     * @var null
     */
    protected $callback_before_insert = null;
    /**
     * @var null
     */
    protected $callback_after_insert = null;
    /**
     * @var null
     */
    protected $callback_insert = null;
    /**
     * @var null
     */
    protected $callback_before_update = null;
    /**
     * @var null
     */
    protected $callback_after_update = null;
    /**
     * @var null
     */
    protected $callback_update = null;
    /**
     * @var null
     */
    protected $callback_before_delete = null;
    /**
     * @var null
     */
    protected $callback_after_delete = null;
    /**
     * @var null
     */
    protected $callback_delete = null;
    /**
     * @var null
     */
    protected $callback_before_restore = null;
    /**
     * @var null
     */
    protected $callback_after_restore = null;
    /**
     * @var null
     */
    protected $callback_restore = null;
    /**
     * @var array
     */
    protected $callback_column = [];
    /**
     * @var array
     */
    protected $callback_add_field = [];
    /**
     * @var array
     */
    protected $callback_edit_field = [];
    /**
     * @var array
     */
    protected $callback_read_field = [];
    /**
     * @var null
     */
    protected $callback_upload = null;
    /**
     * @var null
     */
    protected $callback_before_upload = null;
    /**
     * @var null
     */
    protected $callback_after_upload = null;

    protected $callback_before_activate = null;
    protected $callback_after_activate = null;
    protected $callback_before_inactivate = null;
    protected $callback_after_inactivate = null;

    protected $callback_before_lock = null;
    protected $callback_after_lock = null;
    protected $callback_before_unlock = null;
    protected $callback_after_unlock = null;

    /* Validators */
    /**
     * @var null
     */
    protected $callback_validator = null;

    // Initialize Library Crud
    /**
     * @param null $table
     * @param null $table_title
     */
    public function __construct($table = null, $table_title = null) {
        $this->url = Request::url();

        if (isset($table)) {
            $this->table_name = $table;
            if (Schema::hasTable($table)) {
                $this->table_exist = TRUE;
                $this->primary_key = $this->get_primary_key($table);

                // Set Table Name to parent Class
                parent::setTable_name($table);
            }
        } else {
            die('You must declare and existing table name');
        }

        // Add Custom Table Title
        $this->table_title = isset($table_title) ? $table_title : $table;

        /* RETURN DEFAULT VALUES */
        $this->table_data = [
            'table_title' => $this->table_title,
            'table_name'  => $this->table_name,
        ];

        /* GET DEFAULT ACTIONS TITLE AND ROWS ACTIONS */
        $this->title_actions = $this->getDefaultTitleActions();
        $this->actions = $this->getDefaultActions();
    }

    /**
     * @author Eledi Dyrkaj
     * @return object
     */
    function getPrimary_key() {
        return $this->primary_key;
    }

    /**
     * @param $primary_key
     * @author Eledi Dyrkaj
     */
    function setPrimary_key($primary_key) {
        $this->primary_key = $primary_key;
    }

    /**
     * @param $unset_columns
     * @author Eledi Dyrkaj
     */
    function unset_columns($unset_columns) {
        $this->unset_columns = $unset_columns;
    }

    /**
     * @param $unset_columns
     * @author Eledi Dyrkaj
     */
    function unset_edit_columns($unset_columns) {
        $this->unset_edit_columns = $unset_columns;
    }

    /**
     * @param $unset_columns
     * @author Eledi Dyrkaj
     */
    function unset_read_columns($unset_columns) {
        $this->unset_read_columns = $unset_columns;
    }

    /**
     * @param $unset_columns
     * @author Eledi Dyrkaj
     */
    function unset_add_columns($unset_columns) {
        $this->unset_add_columns = $unset_columns;
    }

    //<editor-fold desc="MANAGE ADDITIONAL SCRIPTS">
    /**
     * @param $script
     * @author Eledi Dyrkaj
     */
    function show_script($script) {
        $this->show_script = $script;
    }

    /**
     * @param $script
     * @author Eledi Dyrkaj
     */
    function add_script($script) {
        $this->add_script = $script;
    }

    /**
     * @param $script
     * @author Eledi Dyrkaj
     */
    function edit_script($script) {
        $this->edit_script = $script;
    }

    /**
     * @param $script
     * @author Eledi Dyrkaj
     */
    function read_script($script) {
        $this->read_script = $script;
    }

    //</editor-fold>
    //<editor-fold desc="MANAGE ACTIONS">
    /**
     * @author Eledi Dyrkaj
     * @return mixed
     */
    function getUnset_actions() {
        return $this->unset_actions;
    }

    /**
     * @param $unset_actions
     * @author Eledi Dyrkaj
     */
    function setUnset_actions($unset_actions) {
        $this->unset_actions = $unset_actions;
    }

    /**
     * @param $action
     * @author Eledi Dyrkaj
     */
    function add_title_action($action) {
        if (is_array($this->title_actions)) {
            $key = key($action);
            $this->title_actions[$key] = $action[$key];
        } else {
            $this->title_actions = $action;
        }
    }

    /**
     * @param $action
     * @author Eledi Dyrkaj
     */
    function add_action($action) {
        if (is_array($this->actions)) {
            $key = key($action);
            $this->actions[$key] = $action[$key];
        } else {
            $this->actions = $action;
        }
    }

    /**
     * @author Eledi Dyrkaj
     * @return array
     */
    function getTitle_actions() {
        if (isset($this->unset_title_action)) {
            if (is_array($this->unset_title_action)) {
                foreach ($this->unset_title_action as $title_action) {
                    if (array_key_exists($title_action, $this->title_actions)) {
                        unset($this->title_actions[$title_action]);
                    }
                }
            }
        }

        return $this->title_actions;
    }

    /**
     * @param $title_actions
     * @author Eledi Dyrkaj
     */
    function setTitle_actions($title_actions) {
        $this->title_actions = $title_actions;
    }

    /**
     * Getter and Setter for private variables
     * used as actions buttons in CRUD
     * @author Eledi Dyrkaj
     * @param $action_add
     */

    function setAction_add($action_add) {
        $this->action_add = $action_add;
    }

    function getAction_add() {
        return $this->action_add;
    }

    function setAction_read($action_read) {
        $this->action_read = $action_read;
    }

    function getAction_read() {
        return $this->action_read;
    }

    function setAction_edit($action_edit) {
        $this->action_edit = $action_edit;
    }

    function getAction_edit() {
        return $this->action_edit;
    }

    function setAction_delete($action_delete) {
        $this->action_delete = $action_delete;
    }

    function getAction_delete() {
        return $this->action_delete;
    }

    function getAction_restore() {
        return $this->action_restore;
    }

    function setAction_restore($action_restore) {
        $this->action_restore = $action_restore;
    }

    function setAction_activate($object) {
        $this->action_activate = $object;
    }

    function getAction_activate() {
        return $this->action_activate;
    }

    function setAction_inactivate($object) {
        $this->action_inactivate = $object;
    }

    function getAction_inactivate() {
        return $this->action_inactivate;
    }

    function setAction_lock($object) {
        $this->action_lock = $object;
    }

    function getAction_lock() {
        return $this->action_lock;
    }

    function setAction_unlock($object) {
        $this->action_unlock = $object;
    }

    function getAction_unlock() {
        return $this->action_unlock;
    }

    /**
     * @author Eledi Dyrkaj
     * @return array
     */
    function getActions() {
        if (isset($this->unset_action)) {
            if (is_array($this->unset_action)) {
                foreach ($this->unset_action as $action) {
                    if (array_key_exists($action, $this->actions)) {
                        unset($this->actions[$action]);
                    }
                }
            }
        }

        return $this->actions;
    }

    /**
     * @param $actions
     * @author Eledi Dyrkaj
     */
    function setActions($actions) {
        $this->actions = $actions;
    }

    //</editor-fold>

    /**
     * @author Eledi Dyrkaj
     * @return mixed
     */
    function getState() {
        return $this->state;
    }

    /**
     * @param $state
     * @author Eledi Dyrkaj
     */
    function setState($state) {
        $this->state = $state;
    }

    /**
     * @param null $post_parameters
     * @author Eledi Dyrkaj
     * @return null
     */
    function getColumns_data($post_parameters = null) {
        $columns_data = $this->get_columns_data($this->table_name, $post_parameters);
        return $columns_data;
    }

    /**
     * @author Eledi Dyrkaj
     * @return array
     */
    function getColumns() {
        if (!isset($this->columns)) {
            $this->columns = $this->get_columns_names($this->table_name);
        }

        // Remove Columns From Unset
        if (is_array($this->unset_columns)) {
            if (!empty($this->unset_columns) && !in_array($this->getState(), [
                    self::ADD,
                    self::EDIT,
                    self::READ
                ])
            ) {
                foreach ($this->unset_columns as $column) {
                    if (($key = array_search($column, $this->columns)) !== FALSE) {
                        unset($this->columns[$key]);
                    }
                }
            }
        }

        if (is_array($this->unset_edit_columns)) {
            if (!empty($this->unset_edit_columns) && $this->getState() == self::EDIT) {
                foreach ($this->unset_edit_columns as $column) {
                    if (($key = array_search($column, $this->columns)) !== FALSE) {
                        unset($this->columns[$key]);
                    }
                }
            }
        }

        if (is_array($this->unset_read_columns)) {
            if (!empty($this->unset_read_columns) && $this->getState() == self::READ) {
                foreach ($this->unset_read_columns as $column) {
                    if (($key = array_search($column, $this->columns)) !== FALSE) {
                        unset($this->columns[$key]);
                    }
                }
            }
        }

        if (is_array($this->unset_add_columns)) {
            if (!empty($this->unset_add_columns) && $this->getState() == self::ADD) {
                foreach ($this->unset_add_columns as $column) {
                    if (($key = array_search($column, $this->columns)) !== FALSE) {
                        unset($this->columns[$key]);
                    }
                }
            }
        }

        if (in_array($this->getState(), [
            self::ADD,
            self::EDIT,
            self::READ
        ])) {
            if (($key = array_search($this->primary_key, $this->columns)) !== FALSE) {
                unset($this->columns[$key]);
            }
        }

        if (isset($this->display_as)) {
            foreach ($this->display_as as $key => $value) {
                unset($this->columns[$key]);
                $this->columns[$key] = $value;
            }
        }

        return $this->columns;
    }

    /**
     * @author Eledi Dyrkaj
     * @return type
     */
    function getColumnsType() {
        return $this->get_columns_type($this->table_name, $this->columns);
    }

    /**
     * @author Eledi Dyrkaj
     * @return string
     */
    function getColumnsClass() {
        return $this->get_columns_class($this->table_name, $this->columns);
    }

    /**
     * @author Eledi Dyrkaj
     * @return string
     */
    function getColumnsValidation() {
        return $this->get_columns_validation($this->table_name, $this->columns);
    }

    /**
     * @author Eledi Dyrkaj
     * @return array
     */
    function getColumnsRequired() {
        return $this->get_columns_required($this->table_name, $this->columns);
    }

    /**
     * @param $columns
     * @author Eledi Dyrkaj
     */
    function setColumns($columns) {
        $this->columns = $columns;
    }

    //<editor-fold desc="Display Crud Options">
    /**
     * @author Eledi Dyrkaj
     * @return null
     */
    public function getTable() {
        return $this->table_name;
    }

    /**
     * @internal param null $table_name
     * @author Eledi Dyrkaj
     * @param null $table
     */
    function set_table($table = null) {
        if (isset($table)) {
            $this->table_name = $table;
        }
    }

    /**
     * @param null $layout
     * @author Eledi Dyrkaj
     */
    function set_layout($layout = null) {
        if (isset($layout)) {
            $this->layout = "layout.$layout";
        }
    }

    /**
     *
     * Changes the displaying label of the field
     * @param $field_name
     * @param $display_as
     * @return void
     * @author Eledi Dyrkaj
     */
    public function display_as($field_name, $display_as = null) {
        if (is_array($field_name)) {
            foreach ($field_name as $field => $display_as) {
                $this->display_as[$field] = $display_as;
            }
        } elseif ($display_as !== null) {
            $this->display_as[$field_name] = $display_as;
        }
        return $this;
    }

    /**
     * Is the last function called from controllers to render display of grid.
     * Generate View of Grid
     * @return html grid
     * @author Eledi Dyrkaj
     */
    function render() {
        if ($this->table_exist == FALSE) {
            $this->table_data['message'] = (object)['text' => "Table Not Found", 'type' => "danger"];
            return $this->get_show_form($this->table_data);
        }

        // Fill Default Data
        $this->prepare_data();

        // Check param method to detect specify action
        $view = null;
        if (Request::has('method')) {
            if (Request::isMethod('POST')) {
                if (!Request::ajax()) {
                    $view = $this->prepare_action();
                } else {
                    return $this->prepare_action();
                }
            } else {
                if (!Request::ajax()) {
                    $view = $this->prepare_action();
                } else {
                    return $this->prepare_action();
                }
            }
        } else {
            $view = $this->prepare_view();
        }

        return $view->render();
    }

    /**
     * Prepare data to send to grid of crud
     */
    function prepare_data() {
        if (Request::is('*/add')) {
            $this->state = self::ADD;
        } elseif (Request::is('*/delete*')) {
            $this->state = self::DELETE;
        } elseif (Request::is('*/restore*')) {
            $this->state = self::RESTORE;
        } elseif (Request::is('*/edit*')) {
            $this->state = self::EDIT;
        } elseif (Request::is('*/read*')) {
            $this->state = self::READ;
        } elseif (Request::is('*/show')) {
            $this->state = self::SHOW;
        } elseif (Request::is('*/activate*')) {
            $this->state = self::ACTIVATE;
        } elseif (Request::is('*/inactivate*')) {
            $this->state = self::INACTIVATE;
        } elseif (Request::is('*/lock*')) {    // Lock account
            $this->state = self::LOCK;
        } elseif (Request::is('*/unlock*')) {    // Unlock account
            $this->state = self::UNLOCK;
        } else {
            $this->state = self::SHOW;
        }

        $this->table_data['columns'] = $this->getColumns();
        $this->table_data['columns_type'] = $this->getColumnsType();
        $this->table_data['columns_class'] = $this->getColumnsClass();
        $this->table_data['columns_required'] = $this->getColumnsRequired();
        $this->table_data['columns_validation'] = $this->getColumnsValidation();
        $this->table_data['rows'] = $this->getColumns_data();
        $this->table_data['primary_key'] = $this->getPrimary_key();
        $this->table_data['actions'] = !isset($this->unset_actions) ? $this->getActions() : null;
        $this->table_data['title_actions'] = !isset($this->unset_title_actions) ? $this->getTitle_actions() : null;
    }

    /**
     * Prepare method actions when returned a post from crud
     */
    function prepare_action() {
        if (Request::has('method')) {
            $data = null;
            $method = Input::only('method');

            switch ($method['method']) {
                case 'add':
                    $data = $this->save_data();
                    break;
                case 'edit':
                    $data = $this->update_data();
                    break;
                case 'delete':
                    $data = $this->delete_data();
                    break;
                case 'restore':
                    $data = $this->restore_data();
                    break;
                case 'activate':
                    $data = $this->activate_data();
                    break;
                case 'inactivate':
                    $data = $this->inactivate_data();
                    break;
                case 'lock':
                    $data = $this->lock_data();
                    break;
                case 'unlock':
                    $data = $this->unlock_data();
                    break;
            }

            if (isset($method)) {
                // Show message
                Session::flash('messages', json_encode($data));
            }

            if (Request::ajax()) {
                return Response::json($data);
            } else {
                $this->table_data['rows'] = $this->getColumns_data();
                $table_data = $this->table_data;
                return $this->get_show_form($table_data);
            }
        }
    }

    /**
     * @author Eledi Dyrkaj
     * @return string|type
     */
    function prepare_view() {
        $view = null;

        if (Request::is('*/add')) {
            $this->table_data['columns'] = $this->getColumns();
            if (isset($this->add_script)) {
                $this->table_data['scripts'] = $this->add_script;
            }
            $view = $this->get_add_form($this->table_data);
        } elseif (Request::is('*/delete*')) {
            $this->table_data['rows'] = $this->getColumns_data(Input::all());
            $view = $this->get_delete_form($this->table_data);
        } elseif (Request::is('*/edit*')) {
            $this->table_data['rows'] = $this->getColumns_data(Input::all());
            $this->table_data['columns'] = $this->getColumns();
            $view = $this->get_edit_form($this->table_data);
        } elseif (Request::is('*/read*')) {
            $this->table_data['rows'] = $this->getColumns_data(Input::all());
            $view = $this->get_read_form($this->table_data);
        } elseif (Request::is('*/show')) {
            $this->table_data['columns'] = $this->getColumns();
            unset($this->table_data['message']);
            $view = $this->get_show_form($this->table_data);
        } else {
            $this->table_data['columns'] = $this->getColumns();
            unset($this->table_data['message']);
            $view = $this->get_show_form($this->table_data);
        }

        return $view;
    }

    //</editor-fold>
    //<editor-fold desc="Default Methods">

    /**
     * Update Data Method
     * @author Eledi Dyrkaj
     */
    public function update_data() {
        $message = null;

        // Remove old data
        unset($this->table_data['rows']);

        // Get Input data without method to be used in query db
        $post_data = Input::except('method');

        try {
            // Callback before update
            if ($this->callback_before_update !== null) {
                $callback_return = call_user_func($this->callback_before_update, $post_data);

                if (!empty($callback_return) && is_array($callback_return)) {
                    $post_data = $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            if (isset($this->callback_validator)) {
                $validator = call_user_func($this->callback_validator, $post_data);
                if (isset($validator)) {
                    if ($validator->fails()) {
                        if ($this->isAjaxEnabled() == true) {
                            return Redirect::back()->withErrors($validator)->withInput();
                        } else {
                            return ['status' => 'danger', 'data' => $validator->messages()->all()];
                        }
                    }
                }
            }

            // Do Update in DB
            $update_data = $post_data;
            unset($update_data[$this->primary_key]);
            unset($update_data['_token']);

            $results = DB::table($this->table_name)->where($this->primary_key, $post_data[$this->primary_key])->update($update_data);

            // Callback after update
            if ($this->callback_after_update !== null) {
                $callback_return = call_user_func($this->callback_after_update, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    $post_data = $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            $message = [
                'status' => 'success',
                'data'   => trans('crud.success_update')
            ];
        } catch (\Exception $exc) {
            $message = [
                'status' => 'danger',
                'data'   =>  $exc->getMessage()
            ];
        }

        // Retrieve new data
        return $message;
    }

    /**
     * Save Data Method
     * @author  Eledi Dyrkaj
     */
    public function save_data() {
        $message = null;
        $resultID = -1;

        // Remove old data
        unset($this->table_data['rows']);

        // Get Input data without method to be used in query db
        $post_data = Input::except('method');

        // Insert new Row
        try {
            if ($this->callback_before_insert !== null) {
                $callback_return = call_user_func($this->callback_before_insert, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    $post_data = $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            if (isset($this->callback_validator)) {
                $validator = call_user_func($this->callback_validator, $post_data);
                if (isset($validator)) {
                    if ($validator->fails()) {
                        if ($this->isAjaxEnabled() == true) {
                            return [
                                'status' => 'danger',
                                'data'   => $validator->messages()->all()
                            ];

                        } else {
                            return Redirect::back()->withErrors($validator)->withInput();
                        }
                    }
                }
            }

            if (!empty($post_data)) {
                // Used to avoid SQL error foreign keys check
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $table_name = $this->table_name;
                // Check if columns exist in database table columns
                foreach ($post_data as $p_data => $p_val) {
                    if (!in_array($p_data, $this->get_columns_names($table_name))) {
                        unset($post_data[$p_data]);
                    }
                }

                $resultID = DB::table($table_name)->insertGetId($post_data);
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            if ($this->callback_after_insert !== null) {
                $post_data['inserted_id'] = $resultID;

                $callback_return = call_user_func($this->callback_after_insert, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    $post_data = $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            $message = [
                'status' => 'success',
                'data'   => trans('crud.success_save')
            ];
        } catch (\Exception $exc) {
            $message = [
                'status' => 'danger',
                'data'   => $exc->getMessage()
            ];
        }

        return $message;
    }

    /**
     * @author Eledi Dyrkaj
     * @return bool
     */
    public function restore_data() {
        $message = null;
        // Remove old data
        unset($this->table_data['rows']);

        // Get Input data without method to be used in query db
        $post_data = Input::except('method');

        $primary_key = $this->getPrimary_key();
        $primary_key_val = $post_data[$primary_key];

        try {
            // Callback Before Restore
            if ($this->callback_before_restore !== null) {
                $callback_return = call_user_func($this->callback_before_restore, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) $post_data = $callback_return; elseif ($callback_return === FALSE) return FALSE;
            }

            // Restore Entry : Check Before if exist soft deleteable
            if (Schema::hasColumn($this->table_name, 'deleted_at')) {
                DB::table($this->table_name)->where($this->primary_key, $primary_key_val)->update(['deleted_at' => null]);
            }

            // Callback After Restore
            if ($this->callback_after_restore !== null) {
                $callback_return = call_user_func($this->callback_after_restore, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) return $callback_return; elseif ($callback_return === FALSE) return FALSE;
            }

            $message = [
                'status' => 'success',
                'data'   => trans('crud.success_restore')
            ];
        } catch (\Exception $exc) {
            $message = [
                'status' => 'danger',
                'data'   => $exc->getMessage()
            ];
        }

        return $message;
    }

    /**
     *  Delete Data Method
     */
    public function delete_data() {
        $message = null;

        // Remove old data
        unset($this->table_data['rows']);

        // Get Input data without method to be used in query db
        $post_data = Input::except('method');

        $primary_key = $this->getPrimary_key();
        $primary_key_val = $post_data[$primary_key];

        try {
            if ($this->table_name == 'users' && Auth::User()->id == $primary_key_val) {
                $this->table_data['message'] = [
                    'status' => 'danger',
                    'data'   => trans('crud.error_remove_user')
                ];
            } else {
                // Callback Before Delete
                if ($this->callback_before_delete !== null) {
                    $callback_return = call_user_func($this->callback_before_delete, $post_data);
                    if (!empty($callback_return) && is_array($callback_return)) $post_data = $callback_return; elseif ($callback_return === FALSE) return FALSE;
                }

                // Delete Entry : Check Before if exist soft deleteable
                if (Schema::hasColumn($this->table_name, 'deleted_at')) {
                    DB::table($this->table_name)->where($this->primary_key, $post_data[$this->primary_key])->update(['deleted_at' => date('Y-m-d H:i:s')]);
                } else {
                    // Force Deleted
                    DB::table($this->table_name)->where([$primary_key => $primary_key_val])->delete();
                }

                // Callback After Delete
                if ($this->callback_after_delete !== null) {
                    $callback_return = call_user_func($this->callback_after_delete, $post_data);
                    if (!empty($callback_return) && is_array($callback_return)) {
                        $post_data = $callback_return;
                        return $post_data;
                    } elseif ($callback_return === FALSE) {
                        return FALSE;
                    }
                }
            }

            $message = [
                'status' => 'success',
                'data'   => trans('crud.success_delete')
            ];
        } catch (\Exception $exc) {
            $message = [
                'status' => 'danger',
                'data'   => $exc->getMessage()
            ];
        }

        return $message;
    }

    /**
     * @author Eledi Dyrkaj
     * @return bool
     */
    public function lock_data() {
        $message = null;

        // Remove old data
        unset($this->table_data['rows']);

        // Get Input data without method to be used in query db
        $post_data = Input::except('method');

        $primary_key = $this->getPrimary_key();
        $primary_key_val = $post_data[$primary_key];

        try {
            // Callback Before: Lock
            if ($this->callback_before_lock !== null) {
                $callback_return = call_user_func($this->callback_before_lock, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    $post_data = $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            // Activate Entry : Check Before if exist column active
            if (Schema::hasColumn($this->table_name, 'locked')) {
                DB::table($this->table_name)->where($this->primary_key, $primary_key_val)->update(['locked' => 1]);
            }
            if (Schema::hasColumn($this->table_name, 'locked_date')) {
                DB::table($this->table_name)->where($this->primary_key, $primary_key_val)->update(['locked_date' => Carbon::now()]);
            }

            // Callback After: Lock
            if ($this->callback_after_lock !== null) {
                $callback_return = call_user_func($this->callback_after_lock, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    return $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            $message = [
                'status' => 'success',
                'data'   => trans('crud.locked')
            ];
        } catch (\Exception $exc) {
            $message = [
                'status' => 'danger',
                'data'   => $exc->getMessage()
            ];
        }

        return $message;
    }

    /**
     * @author Eledi Dyrkaj
     * @return bool
     */
    public function unlock_data() {
        $message = null;

        // Remove old data
        unset($this->table_data['rows']);

        // Get Input data without method to be used in query db
        $post_data = Input::except('method');

        $primary_key = $this->getPrimary_key();
        $primary_key_val = $post_data[$primary_key];

        try {
            // Callback Before: UnLock
            if ($this->callback_before_unlock !== null) {
                $callback_return = call_user_func($this->callback_before_unlock, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    $post_data = $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            // Activate Entry : Check Before if exist column active
            if (Schema::hasColumn($this->table_name, 'locked')) {
                DB::table($this->table_name)->where($this->primary_key, $primary_key_val)->update(['locked' => 0]);
            }
            if (Schema::hasColumn($this->table_name, 'locked_date')) {
                DB::table($this->table_name)->where($this->primary_key, $primary_key_val)->update(['locked_date' => null]);
            }

            // Callback After: Lock
            if ($this->callback_after_unlock !== null) {
                $callback_return = call_user_func($this->callback_after_unlock, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    return $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            $message = [
                'status' => 'success',
                'data'   => trans('crud.unlock')
            ];
        } catch (\Exception $exc) {
            $message = [
                'status' => 'danger',
                'data'   => $exc->getMessage()
            ];
        }

        return $message;
    }

    /**
     * @author Eledi Dyrkaj
     * @return bool
     */
    public function activate_data() {
        $message = null;

        // Remove old data
        unset($this->table_data['rows']);

        // Get Input data without method to be used in query db
        $post_data = Input::except('method');

        $primary_key = $this->getPrimary_key();
        $primary_key_val = $post_data[$primary_key];

        try {
            // Callback Before: Activate
            if ($this->callback_before_activate !== null) {
                $callback_return = call_user_func($this->callback_before_activate, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    $post_data = $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            // Activate Entry : Check Before if exist column active
            if (Schema::hasColumn($this->table_name, 'active')) {
                DB::table($this->table_name)->where($this->primary_key, $primary_key_val)->update(['active' => 1]);
            }

            // Callback After: Activate
            if ($this->callback_after_activate !== null) {
                $callback_return = call_user_func($this->callback_after_activate, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    return $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            $message = [
                'status' => 'success',
                'data'   => trans('crud.success_activation')
            ];
        } catch (\Exception $exc) {
            $message = [
                'status' => 'danger',
                'data'   => $exc->getMessage()
            ];
        }

        return $message;
    }

    /**
     * @author Eledi Dyrkaj
     * @return bool
     */
    public function inactivate_data() {
        $message = null;

        // Remove old data
        unset($this->table_data['rows']);

        // Get Input data without method to be used in query db
        $post_data = Input::except('method');

        $primary_key = $this->getPrimary_key();
        $primary_key_val = $post_data[$primary_key];

        try {
            // Callback Before: InActivate
            if ($this->callback_before_inactivate !== null) {
                $callback_return = call_user_func($this->callback_before_inactivate, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    $post_data = $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            // Activate Entry : Check Before if exist column active
            if (Schema::hasColumn($this->table_name, 'active')) {
                DB::table($this->table_name)->where($this->primary_key, $primary_key_val)->update(['active' => 0]);
            }

            // Callback After: InActivate
            if ($this->callback_after_inactivate !== null) {
                $callback_return = call_user_func($this->callback_after_inactivate, $post_data);
                if (!empty($callback_return) && is_array($callback_return)) {
                    return $callback_return;
                } elseif ($callback_return === FALSE) {
                    return FALSE;
                }
            }

            $message = [
                'status' => 'success',
                'data'   => trans('crud.success_inactivation')
            ];
        } catch (\Exception $exc) {
            $message = [
                'status' => 'danger',
                'data'   => $exc->getMessage()
            ];
        }

        return $message;
    }

    //</editor-fold>
    //<editor-fold desc="Manage Actions">
    /**
     * @param null $actions
     * @author Eledi Dyrkaj
     */
    public function unset_actions($actions = null) {
        if (!isset($actions)) {
            $this->unset_actions = TRUE;
        }
    }

    /**
     * @param null $actions
     * @author Eledi Dyrkaj
     */
    public function unset_action($actions = null) {
        $this->unset_action = $actions;
    }

    /**
     * @param null $actions
     * @author Eledi Dyrkaj
     */
    public function unset_title_action($actions = null) {
        $this->unset_title_action = $actions;
    }

    /**
     * @param null $actions
     * @author Eledi Dyrkaj
     */
    public function unset_title_actions($actions = null) {
        if (!isset($actions)) {
            $this->unset_title_actions = TRUE;
        }
    }


    /**
     * Default Actions Buttons for each CRUD Table
     * @author Eledi Dyrkaj
     * @return array
     */
    public function getDefaultActions() {
        $this->setAction_read((object)[
            'attributes' => [
                'href'              => Request::url() . '/read',
                'class'             => 'btn btn-xs tooltips read-row crud-action btn-purple',
                'tooltip_placement' => 'top',
                'title'             => trans('crud.read'),
                'ajax'              => var_export($this->ajax_enabled, TRUE)
            ],
            'content'    => [
                'icon' => 'fa fa-search',
            ]
        ]);

        $this->setAction_delete((object)[
            'attributes' => [
                'href'              => Request::url() . '/delete',
                'class'             => 'btn btn-xs tooltips delete-row btn-purple',
                'tooltip_placement' => 'top',
                'title'             => trans('crud.delete'),
                'ajax'              => var_export($this->ajax_enabled, TRUE)
            ],
            'content'    => [
                'icon' => 'fa fa-trash',
            ]
        ]);

        if (Schema::hasColumn($this->table_name, 'deleted_at')) {
            $this->setAction_restore((object)[
                'attributes' => [
                    'href'              => Request::url(). '/restore',
                    'class'             => 'btn btn-xs tooltips restore-row btn-purple',
                    'tooltip_placement' => 'top',
                    'title'             => trans('crud.restore'),
                    'ajax'              => var_export($this->ajax_enabled, TRUE)
                ],
                'content'    => [
                    'icon' => 'fa fa-history'
                ]
            ]);
        }

        $this->setAction_edit((object)[
            'attributes' => [
                'href'              => Request::url() . '/edit',
                'class'             => 'btn btn-xs tooltips edit-row crud-action btn-purple',
                'tooltip_placement' => 'top',
                'title'             => trans('crud.edit'),
                'ajax'              => var_export($this->ajax_enabled, TRUE)
            ],
            'content'    => [
                'icon' => 'fa fa-pencil',
            ]
        ]);

        $actions = [
            'read'   => $this->getAction_read(),
            'edit'   => $this->getAction_edit(),
            'delete' => $this->getAction_delete()
        ];

        if (Schema::hasColumn($this->table_name, 'deleted_at')) {
            $actions['restore'] = $this->getAction_restore();
        }

        if (Schema::hasColumn($this->table_name, 'active')) {
            // Set Activation Button
            $this->setAction_activate((object)[
                'attributes' => [
                    'href'              => Request::url(),
                    'method'            => 'activate',
                    'class'             => 'btn btn-xs tooltips active-row crud-action btn-purple',
                    'tooltip_placement' => 'top',
                    'title'             => trans('crud.activate'),
                    'ajax'              => var_export($this->ajax_enabled, TRUE)
                ],
                'content'    => ['icon' => 'fa fa-check-circle']
            ]);

            // Set InActivation Button
            $this->setAction_inactivate((object)[
                'attributes' => [
                    'href'              => Request::url(),
                    'method'            => 'inactivate',
                    'class'             => 'btn btn-xs tooltips inactive-row crud-action btn-purple',
                    'tooltip_placement' => 'top',
                    'title'             => trans('crud.inactivate'),
                    'ajax'              => var_export($this->ajax_enabled, TRUE)
                ],
                'content'    => ['icon' => 'fa fa-minus-circle']
            ]);

            $actions['activate'] = $this->getAction_activate();
            $actions['inactivate'] = $this->getAction_inactivate();
        }

        if (Schema::hasColumn($this->table_name, 'locked')) {
            // Set Locked Button
            $this->setAction_lock((object)[
                'attributes' => [
                    'href'              => Request::url(),
                    'method'            => 'lock',
                    'class'             => 'btn btn-xs tooltips lock-row crud-action btn-purple',
                    'tooltip_placement' => 'top',
                    'title'             => trans('crud.lock'),
                    'ajax'              => var_export($this->ajax_enabled, TRUE)
                ],
                'content'    => ['icon' => 'fa fa-lock']
            ]);

            // Set UnLocked Button
            $this->setAction_unlock((object)[
                'attributes' => [
                    'href'              => Request::url(),
                    'method'            => 'unlock',
                    'class'             => 'btn btn-xs tooltips unlock-row crud-action btn-purple',
                    'tooltip_placement' => 'top',
                    'title'             => trans('crud.unlock'),
                    'ajax'              => var_export($this->ajax_enabled, TRUE)
                ],
                'content'    => ['icon' => 'fa fa-unlock']
            ]);

            $actions['lock'] = $this->getAction_lock();
            $actions['unlock'] = $this->getAction_unlock();
        }

        return $actions;
    }

    /**
     * @author Eledi Dyrkaj
     * @return array
     */
    public function getDefaultTitleActions() {

        /* Default Actions for title panel */
        $this->action_add = (object)[
            'url'               => Request::url() . '/add',
            'class'             => 'btn btn-xs btn-link tooltips crud-action',
            'tooltip_placement' => 'top',
            'label'             => trans('crud.add') . ' ' . trans("crud.$this->table_name"),
            'icon'              => 'fa fa-plus'
        ];

        return ['add' => $this->getAction_add()];
    }

    //</editor-fold>
    //<editor-fold desc="Callback Functions">

    /**
     *
     * @example
     * $crud->callback_validator(array($this, '_callback_function_name'));
     *
     * public function _callback_function_name($post_array) {
     *     $validator = Validator::make($post_array, ManageValidator::user_validation());
     *     return $validator;
     * }
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_validator($callback = null) {
        $this->callback_validator = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_insert($callback = null) {
        $this->callback_before_insert = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_insert($callback = null) {
        $this->callback_after_insert = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_insert($callback = null) {
        $this->callback_insert = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_update($callback = null) {
        $this->callback_before_update = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_update($callback = null) {
        $this->callback_after_update = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_update($callback = null) {
        $this->callback_update = $callback;

        return $this;
    }

    /**
     *
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_delete($callback = null) {
        $this->callback_before_delete = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_delete($callback = null) {
        $this->callback_after_delete = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_delete($callback = null) {
        $this->callback_delete = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_restore($callback = null) {
        $this->callback_before_restore = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_restore($callback = null) {
        $this->callback_after_restore = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_restore($callback = null) {
        $this->callback_restore = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_activate($callback = null) {
        $this->callback_before_activate = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_activate($callback = null) {
        $this->callback_after_activate = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_inactivate($callback = null) {
        $this->callback_before_inactivate = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_inactivate($callback = null) {
        $this->callback_after_inactivate = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_lock($callback = null) {
        $this->callback_before_lock = $callback;
        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_lock($callback = null) {
        $this->callback_after_lock = $callback;
        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_unlock($callback = null) {
        $this->callback_before_unlock = $callback;
        return $this;
    }

    /**
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_unlock($callback = null) {
        $this->callback_after_unlock = $callback;
        return $this;
    }


    /**
     * @param string $column
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_column($column, $callback = null) {
        $this->callback_column[$column] = $callback;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_field($field, $callback = null) {
        $this->callback_add_field[$field] = $callback;
        $this->callback_edit_field[$field] = $callback;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_add_field($field, $callback = null) {
        $this->callback_add_field[$field] = $callback;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_edit_field($field, $callback = null) {
        $this->callback_edit_field[$field] = $callback;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_read_field($field, $callback = null) {
        $this->callback_read_field[$field] = $callback;

        return $this;
    }

    /**
     *
     * Callback that replace the default auto uploader
     *
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_upload($callback = null) {
        $this->callback_upload = $callback;

        return $this;
    }

    /**
     *
     * A callback that triggered before the upload functionality. This callback is suggested for validation checks
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_before_upload($callback = null) {
        $this->callback_before_upload = $callback;

        return $this;
    }

    /**
     *
     * A callback that triggered after the upload functionality
     * @param mixed $callback
     * @return \Crud
     */
    public function callback_after_upload($callback = null) {
        $this->callback_after_upload = $callback;
        return $this;
    }

    //</editor-fold>
}
