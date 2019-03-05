<?php

namespace App\AdminModule\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;


final class UserPasswordFormFactory
{
	use Nette\SmartObject;

	const PASSWORD_MIN_LENGTH = 7;

	/** @var FormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userManager;


	public function __construct(FormFactory $factory, Model\UserManager $userManager)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addHidden('id');

		$passwordInput = $form->addPassword('password', 'Heslo:')
			->setOption('description', sprintf('minimálně %d znaků', self::PASSWORD_MIN_LENGTH))
			->setRequired('Zadejte, prosím, heslo')
			->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);
			
		$form->addPassword('password2', 'Zopakujte heslo')
			->setRequired('Zopakujte, prosím, heslo kvůli ověření')
			->addRule($form::EQUAL, 'Zadaná hesla se neshodují!', $passwordInput);
		
		$form->getElementPrototype()->class[] = 'ajax';

		$form->addSubmit('send', 'Změnit heslo')->setAttribute('class', 'ajax');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			Debugger::barDump($values);
			$this->userManager->changePassword($values['id'], $values['password']);
			$onSuccess();
		};

		return $form;
	}
}
