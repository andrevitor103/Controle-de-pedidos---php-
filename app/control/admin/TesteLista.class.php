<?php

class TesteLista extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'projeto1';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Pedidos';
    private $showMethods = ['onReload', 'onSearch'];

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Pedidos");

        $id_problema = new TEntry('id');
        $descricao = new TEntry('cliente');
        //$cod_setor = new TDBCombo('cod_setor', 'qualidade', 'Setor', 'id_setor', '{id_setor} - {descricao}','id_setor asc'  );
        $providencia = new TEntry('vendedor');
        $valor = new TEntry('valor');
        $status = new TEntry('status');

        $descricao->setMaxLength(100);
        $id_problema->setMaxLength(11);
        $providencia->setMaxLength(100);

        $id_problema->setSize(100);
        $descricao->setSize('100%');
        //$cod_setor->setSize('100%');
        $providencia->setSize('100%');
        
        $bt2a = new TButton('bt2a');
        
        $bt2a->setLabel('Warning');

        $bt2a->class = 'btn btn-warning btn-sm';

        $row1 = $this->form->addFields([new TLabel("Código", null, '14px', null, '100%'),$id_problema],[new TLabel("Cliente", null, '14px', null, '100%'),$descricao]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Valor", null, '14px', null, '100%'),$valor],[new TLabel("Vendedor", null, '14px', null, '100%'),$providencia]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $startHidden = true;

        if(TSession::getValue('TesteLista_expand_start_hidden') === false)
        {
            $startHidden = false;
        }
        elseif(TSession::getValue('TesteLista_expand_start_hidden') === true)
        {
            $startHidden = true; 
        }
        $expandButton = $this->form->addExpandButton("Expandir", 'fas:expand #000000', $startHidden);
        $expandButton->addStyleClass('btn-default');
        $expandButton->setAction(new TAction([$this, 'onExpandForm'], ['static'=>1]), "Expandir");
        $this->form->addField($expandButton);

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Cadastrar", new TAction(['teste', 'onShow']), 'fas:plus #69aa46');

        //$btn_modal = $this->form->addAction("ModalCadastro", new TAction([$this, 'onNewClient']), 'fas:plus #69aa46');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id_problema = new TDataGridColumn('id', "Pedido código", 'center' , '70px');
        $column_descricao = new TDataGridColumn('cliente', "cliente", 'left');
        $column_cod_setor = new TDataGridColumn('valor', "Valor", 'left');
        $column_providencia = new TDataGridColumn('vendedor', "vendedor", 'left');
        $column_status = new TDataGridColumn('status', "Situação", 'left');

        $order_id_problema = new TAction(array($this, 'onReload'));
        $order_id_problema->setParameter('order', 'id');
        $column_id_problema->setAction($order_id_problema);

        $this->datagrid->addColumn($column_id_problema);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_cod_setor);
        $this->datagrid->addColumn($column_providencia);
        $this->datagrid->addColumn($column_status);    

        $action_onEdit = new TDataGridAction(array('teste', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('TesteLista', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup;
        $panel->add($this->datagrid);

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TBreadCrumb::create(["Administração","Relação de pedidos"]));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public static function onNewClient($param = null) 
    {
        try 
        {
            $class = 'teste';
            $method = 'onShow';
            //TSession::setValue('sessao_pre_cadastro', $param);
            TScript::create("('index.php?class={$class}')");
            AdiantiCoreApplication::loadPage('teste', 'onShow');
                    //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new Pedido($key, FALSE); 

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->cliente) AND ( (is_scalar($data->cliente) AND $data->cliente !== '') OR (is_array($data->cliente) AND (!empty($data->cliente)) )) )
        {

            $filters[] = new TFilter('cliente', 'like', "%{$data->cliente}%");// create the filter 
        }

        if (isset($data->valor) AND ( (is_scalar($data->valor) AND $data->valor !== '') OR (is_array($data->valor) AND (!empty($data->valor)) )) )
        {

            $filters[] = new TFilter('valor', '=', $data->valor);// create the filter 
        }

        if (isset($data->providencia) AND ( (is_scalar($data->providencia) AND $data->providencia !== '') OR (is_array($data->providencia) AND (!empty($data->providencia)) )) )
        {

            $filters[] = new TFilter('providencia', 'like', "%{$data->providencia}%");// create the filter 
        }

        $param = array();
        $param['offset']     = 0;
        $param['first_page'] = 1;

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload($param);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'qualidade'
            TTransaction::open(self::$database);

            // creates a repository for Problemas
            $repository = new TRepository(self::$activeRecord);
            $limit = 20;

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid

                    $this->datagrid->addItem($object);

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public static function onExpandForm($param = null)
    {
        try
        {
            $startHidden = true;

            if(TSession::getValue('TesteLista_expand_start_hidden') === false)
            {
                TSession::setValue('TesteLista_expand_start_hidden', true);
            }
            elseif(TSession::getValue('TesteLista_expand_start_hidden') === true)
            {
                TSession::setValue('TesteLista_expand_start_hidden', false);
            }
            else
            {
                TSession::setValue('TesteLista_expand_start_hidden', !$startHidden);
            }

        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

}

