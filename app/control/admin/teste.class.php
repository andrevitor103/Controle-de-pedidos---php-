<?php

class teste extends TWindow {


    protected $form;
    private $formFields = [];
    private static $database = 'projeto1';
    private static $activeRecord = 'Pedido';
    private static $primaryKey = 'id';
    private static $formName = 'form_pedido';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Pedidos");

        $id_problema = new TEntry('id');
        $descricao = new TEntry('cliente');
        //$cod_setor = new TDBCombo('cod_setor', 'qualidade', 'Setor', 'id_setor', '{id_setor} - {descricao} ','id_setor asc'  );
        $providencia = new TEntry('vendedor');
        $valor = new TEntry('valor');
        $status = new TCombo('[]');
        $status->addItems(['1'=>'Aprovado','2'=>'Pendente']);

        /*$descricao->addValidation("Descricao", new TRequiredValidator()); 
        $cod_setor->addValidation("Cod setor", new TRequiredValidator()); 
        $providencia->addValidation("Providencia", new TRequiredValidator()); */
        //$status->setValue('1');
        $valor->addValidation("Valor", new TRequiredValidator());

        $id_problema->setEditable(false);

        $descricao->forceUpperCase();
        $providencia->forceUpperCase();

        $descricao->setMaxLength(100);
        $id_problema->setMaxLength(11);
        $providencia->setMaxLength(100);

        $id_problema->setSize(110);
        $descricao->setSize('100%');
        //$cod_setor->setSize('100%');
        $providencia->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Pedido", null, '14px', null),$id_problema],[new TLabel("Cliente", '#ff0000', '14px', null, '100%'),$descricao]);
        $row1->layout = [' col-sm-6',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Valor", '#ff0000', '14px', null, '100%'),$valor],[new TLabel("Vendedor", '#ff0000', '14px', null, '100%'),$providencia]);
        $row2 = $this->form->addFields([new TLabel("Situação", '#ff0000', '14px', null, '100%'),$status]);
        $row2->layout = [' col-sm-6','col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');

        $btn_back = $this->form->addAction("Voltar", new TAction([$this, 'backPage']), 'fas:arrow-circle-left #dd5a43');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(["administração","teste"]));
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Pedido(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->id_problema = $object->id_problema; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            /**
            // To define an action to be executed on the message close event:
            $messageAction = new TAction(['className', 'methodName']);
            **/

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle'); 

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Pedido($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function backPage( $param )
    {
        header('Location: index.php?class=TesteLista');
    }

    public function onShow($param = null)
    {

    }

    public static function onClose()
    {
        "<script> console.log('aaaa'); </script>";
        header('Location: index.php');
        //parent::closeWindow();
    } 

}
