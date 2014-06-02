<?php

class Application_Form_Login extends Ns_Form
{

    public function init()
    {
		$this->setAttrib('id','login');
		$this->addElement('text','user',array(
			'label'=>'Username',
			'placeholder'=>'Username',
			'required'=>true,
			'decorators'=>array(
				array(
					'HtmlTag',array(
						'tag'=>'div',
						'class'=>'text-danger',
					)
				)
			),
		));
		
		$this->addElement('password','password',array(
			'label'=>'Wachtwoord',
			'placeholder'=>'Wachtwoord',
			'class'=>'text',
			'required'=>true,
		));

		$this->addElement('submit','signup',array(
			'label'=>'Aanmelden',
			'class'=>'btn btn-success btn-lg',
		));
		
    }


}

