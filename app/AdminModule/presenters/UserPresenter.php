<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms;
use Nette\Application\UI\Form;
use App\Model;
use Tracy\Debugger;


final class UserPresenter extends BasePresenter	
{
	/** @var Forms\UserProfileFormFactory */
	private $userProfileFactory;

	/** @var Forms\UserPasswordFormFactory */
	private $userPasswordFactory;

	/** @var Module\UserManager */
	private $userManager;

	/** @persistent */
    public $backlink = '';

	public function __construct(Forms\UserProfileFormFactory $userProfileFactory, Forms\UserPasswordFormFactory $userPasswordFactory, Model\UserManager $userManager)
	{
		$this->userProfileFactory = $userProfileFactory;
		$this->userPasswordFactory = $userPasswordFactory;
		$this->userManager = $userManager;
	}

	/**
	 * User Profile form factory.
	 * @return Form
	 */
	protected function createComponentUserProfileForm()
	{
		return $this->userProfileFactory->create(function () {          
			$this->redirect('list');
		});
	}

	/**
	 * User Password form factory.
	 * @return Form
	 */
	protected function createComponentUserPasswordForm()
	{
		return $this->userPasswordFactory->create(function () {   
			//Debugger::barDump($this->backlink); 
			$this->restoreRequest($this->backlink);       
			//$this->redirect('Homepage:');
		});
	}

	public function handleDelete($id)
	{
		if ($this->userManager->delete($id))		
			$this->flashMessage('Záznam byl úspěšně smazán','alert-success');
		else
			$this->flashMessage('Záznam se nepodařilo vymazat','alert-danger'); 	
		$this->redirect('User:list');	
	}

	public function handleBanned($id)
	{
		$this->userManager->banned($id);
        if ($this->isAjax()) {
			Debugger::barDump('ajax'); 
		}
		$this->redirect('User:list');		
	}

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

	public function renderList()
	{
		$this->template->data = $this->userManager->getAll();
		if ($this->isAjax()) {
            $this->redrawControl("list");
		}
	}

	public function renderProfile($id)
	{
		$data = $this->userManager->get($id);
		$this->template->data = $data;
		$this['userProfileForm']->setDefaults($data->toArray());

		if ($this->isAjax()) {
			Debugger::barDump('ajax'); 
			$this->payload->isModal = TRUE;
			$this->redrawControl("modal");
		}
	}

	public function renderPassword($id)
	{
		$data = $this->userManager->get($id);
		$this->template->data = $data;
		$this['userPasswordForm']->setDefaults($data->toArray());
	}
}
