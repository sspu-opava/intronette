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

	/** @var Module\UserManager */
	private $userManager;


	public function __construct(Forms\UserProfileFormFactory $userProfileFactory, Model\UserManager $userManager)
	{
		$this->userProfileFactory = $userProfileFactory;
		$this->userManager = $userManager;
	}

	/**
	 * User Profile form factory.
	 * @return Form
	 */
	protected function createComponentUserProfileForm()
	{
		return $this->userProfileFactory->create(function () {          
			$this->redirect('Homepage:');
		});
	}

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}


	public function renderProfile($id)
	{
		$data = $this->userManager->get($id);
		$this->template->data = $data;
		$data = $data->toArray();		
        $this['userProfileForm']->setDefaults($data);
	}

}
