<?php
App::uses('AppController', 'Controller');
App::uses('CakePdf','CakePdf.Pdf');
/**
 * Products Controller
 *
 * @property Product $Product
 * @property PaginatorComponent $Paginator
 */
class ProductsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');



/**
 * index method
 *
 * @return void
 */

    public function beforeFilter()
    {
        if (AuthComponent::user('user_role_id') != 1) {
            $this->Session->setFlash('You are not authenticated to view this page','flash/error');
            $this->redirect('/');
        }
        //If Admin
    }

	public function index() {
		$this->Product->recursive = 0;
		$this->set('products', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
	    $this->pdfConfig=array(
	        'orientation'=>'landscape',
            'download'=>true,
            'filename'=>'invoice-2005.pdf'
        );
		if (!$this->Product->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}


	}
	 public function invoice($id=null){
	    $this->Product->id=$id;
	    if($this->Product->exists()){
            throw new NotFoundException(__('Invalid product'));
        }
        $this->set('product', $this->Product->getInvoiceViewData($id));
	    $this->layout ="invoice";

	    $this->pdfConfig=array(
	        'download'=> true,
            'engine'=>'CakePdf.DomPdf',
            'filename'=>'apples.pdf'

        );

     }

/**
 * add method
 *
 * @return void
 */
	public function add() {

		if ($this->request->is('post')) {
			$this->Product->create();
			if ($this->Product->save($this->request->data)) {
				$this->Flash->success(__('The product has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The product could not be saved. Please, try again.'));
			}
		}




	}


/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Product->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Product->save($this->request->data)) {
				$this->Flash->success(__('The product has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The product could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
			$this->request->data = $this->Product->find('first', $options);
		}
		$categories = $this->Product->Category->find('list');
		$this->set(compact('categories'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException(__('Invalid product'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Product->delete()) {
			$this->Flash->success(__('The product has been deleted.'));
		} else {
			$this->Flash->error(__('The product could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
