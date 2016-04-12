<?php
/**
 * This file is part of Onion
 *
 * Copyright (c) 2014-2016, Humberto Lourenço <betto@m3uzz.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Humberto Lourenço nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    Onion
 * @author     Humberto Lourenço <betto@m3uzz.com>
 * @copyright  2014-2016 Humberto Lourenço <betto@m3uzz.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://github.com/m3uzz/onionfw
 */

namespace Onion\Application;
use Onion\Application\Element;
use Onion\Log\Debug;

class Integration
{

	public static function getIntegrateModules ()
	{
		$laIni = Register::getConfigMerged("mods_enable.ini", NULL, false);
		
		$laModules = isset($laIni['mods_integrate']['mod']) ? $laIni['mods_integrate']['mod'] : null;
		
		if (is_array($laModules))
		{
			foreach ($laModules as $lsModule => $lbIsActive)
			{
				if ($lbIsActive)
				{
					$lsPathC = CLIENTE_PATH . DS . 'configs' . DS . "modules" . DS . $lsModule . ".php";
					$lsPathD = CONFIG_PATH . DS . 'modules' . DS . $lsModule . ".php";
					
					if (file_exists($lsPathD))
					{
						include ($lsPathD);
					}
					
					if (file_exists($lsPathC))
					{
						include ($lsPathC);
					}
				}
			}
		}
	}

	public static function getModule ($pmModule)
	{
		// Validando se o parametro se trata de um objeto válido ou uma string
		if ($pmModule instanceof Module)
		{
			return $pmModule;
		}
		elseif (is_string($pmModule))
		{
			// Recuperando registro de resources da sessão
			return Register::getResources($pmModule);
		}
		elseif ($pmModule === null)
		{
			return null;
		}
		else
		{
			throw new Exception('O Module requisitado não é válido ou não existe!');
		}
	}

	public static function getController ($pmController, $pmModule = null)
	{
		// Validando se o parametro se trata de um objeto válido ou uma string
		if ($pmController instanceof Controller)
		{
			return $pmController;
		}
		elseif (is_string($pmController))
		{
			$loModule = self::getModule($pmModule);
			
			if ($loModule !== null)
			{
				return $loModule->getController($pmController);
			}
			
			throw new Exception('É necessário informar um módulo válido que contenha o controller requisitado!');
		}
		else
		{
			throw new Exception('O Controller requisitado não é válido ou não existe!');
		}
	}

	public static function getAction ($pmAction, $pmController = null)
	{
		// Validando se o parametro se trata de um objeto válido ou uma string
		if ($pmAction instanceof Action)
		{
			return $pmAction;
		}
		elseif (is_string($pmAction))
		{
			$loController = self::getController($pmController);
			
			if ($loController !== null)
			{
				return $loController->getAction($pmAction);
			}
			
			throw new Exception('É necessário informar um controller válido que contenha a action requisitada!');
		}
		else
		{
			throw new Exception('A action requisitada não é válida ou não existe!');
		}
	}

	public static function getControllerButton ($pmButton, $pmController = null)
	{
		// Validando se o parametro se trata de um objeto válido ou uma string
		if ($pmButton instanceof Element\Button)
		{
			return $pmButton;
		}
		elseif (is_string($pmButton))
		{
			$loController = self::getController($pmController);
			
			if ($loController !== null)
			{
				return $loController->getButton($pmButton);
			}
			
			throw new Exception('É necessário informar um controller válido que contenha o button requisitado!');
		}
		else
		{
			throw new Exception('O button requisitado não é válido ou não existe!');
		}
	}

	public static function getActionToolbar ($pmButton, $pmAction = null)
	{
		// Validando se o parametro se trata de um objeto válido ou uma string
		if ($pmButton instanceof Element\Button)
		{
			return $pmButton;
		}
		elseif (is_string($pmButton))
		{
			$loAction = self::getAction($pmAction);
			
			if ($loAction !== null)
			{
				return $loAction->getToolbar($pmButton);
			}
			
			throw new Exception('É necessário informar uma action válida que contenha o button requisitado!');
		}
		else
		{
			throw new Exception('O button requisitado não é válido ou não existe!');
		}
	}

	public static function getColAction ($pmColAction, $pmAction = null)
	{
		// Validando se o parametro se trata de um objeto válido ou uma string
		if ($pmColAction instanceof Element\ColAction)
		{
			return $pmColAction;
		}
		elseif (is_string($pmColAction))
		{
			$loAction = self::getAction($pmAction);
			
			if ($loAction !== null)
			{
				return $loAction->getColAction($pmColAction);
			}
			
			throw new Exception('É necessário informar uma action válida que contenha a colAction requisitada!');
		}
		else
		{
			throw new Exception('A colAction requisitada não é válido ou não existe!');
		}
	}

	public static function getColActionBtn ($pmColActionBtn, $pmColAction = null)
	{
		// Validando se o parametro se trata de um objeto válido ou uma string
		if ($pmColActionBtn instanceof Element\ColAction)
		{
			return $pmColActionBtn;
		}
		elseif (is_string($pmColActionBtn))
		{
			$loColAction = self::getColAction($pmColAction);
			
			if ($loColAction !== null)
			{
				return $loColAction->getColActionBtn($pmColActionBtn);
			}
			
			throw new Exception('É necessário informar uma colAction válida que contenha a colActionBtn requisitada!');
		}
		else
		{
			throw new Exception('A colActionBtn requisitada não é válido ou não existe!');
		}
	}

	public static function integrateController ($pmFrom, $pmTo, $pmController, $psControllerNewName = null)
	{
		$loModuleFrom = self::getModule($pmFrom);
		$loModuleTo = self::getModule($pmTo);
		$loController = self::getController($pmController, $loModuleFrom);
		
		if ($loModuleFrom !== null)
		{
			$loController = $loController->copy();
		}
		
		$loModuleTo->addController($loController, $psControllerNewName);
		
		return $loController;
	}

	public static function integrateAction ($pmFrom, $pmTo, $pmAction, $psActionNewName = null)
	{
		if ($pmFrom !== null && is_string($pmFrom))
		{
			$laResourceFrom = explode("/", $pmFrom);
			$loModuleFrom = self::getModule($laResourceFrom[0]);
			$loControllerFrom = self::getController($laResourceFrom[1], $loModuleFrom);
		}
		elseif ($pmFrom !== null && is_obejct($pmFrom))
		{
			$loControllerFrom = self::getController($pmFrom);
		}
		else
		{
			$loControllerFrom = null;
		}
		
		$loAction = self::getAction($pmAction, $loControllerFrom);
		
		if ($loControllerFrom !== null)
		{
			$loAction = $loAction->copy();
		}
		
		if ($pmTo !== null && is_string($pmTo))
		{
			$laResourceTo = explode("/", $pmTo);
			$loModuleTo = self::getModule($laResourceTo[0]);
			$loControllerTo = self::getController($laResourceTo[1], $loModuleTo);
		}
		elseif ($pmTo !== null && is_obejct($pmTo))
		{
			$loControllerTo = self::getController($pmTo);
		}
		else
		{
			$loControllerTo = null;
		}
		
		$loControllerTo->addAction($loAction, $psActionNewName);
		
		return $loAction;
	}

	public static function integrateControllerButton ($pmFrom, $pmTo, $pmButton, $pnButtonNewIndice = null)
	{
		if ($pmFrom !== null && is_string($pmFrom))
		{
			$laResourceFrom = explode("/", $pmFrom);
			$loModuleFrom = self::getModule($laResourceFrom[0]);
			$loControllerFrom = self::getController($laResourceFrom[1], $loModuleFrom);
		}
		elseif ($pmFrom !== null && is_obejct($pmFrom))
		{
			$loControllerFrom = self::getController($pmFrom);
		}
		else
		{
			$loControllerFrom = null;
		}
		
		$loButton = self::getControllerButton($pmButton, $loControllerFrom);
		
		if ($loControllerFrom !== null)
		{
			$loButton = $loButton->copy();
		}
		
		if ($pmTo !== null && is_string($pmTo))
		{
			$laResourceTo = explode("/", $pmTo);
			$loModuleTo = self::getModule($laResourceTo[0]);
			$loControllerTo = self::getController($laResourceTo[1], $loModuleTo);
		}
		elseif ($pmTo !== null && is_obejct($pmTo))
		{
			$loControllerTo = self::getController($pmTo);
		}
		else
		{
			$loControllerTo = null;
		}
		
		$loControllerTo->addButton($loButton, $pnButtonNewIndice);
		
		return $loButton;
	}

	public static function integrateActionToolbar ($pmFrom, $pmTo, $pmButton, $pnButtonNewIndice = null)
	{
		if ($pmFrom !== null && is_string($pmFrom))
		{
			$laResourceFrom = explode("/", $pmFrom);
			$loModuleFrom = self::getModule($laResourceFrom[0]);
			$loControllerFrom = self::getController($laResourceFrom[1], $loModuleFrom);
			$loActionFrom = self::getAction($laResourceFrom[2], $loControllerFrom);
		}
		elseif ($pmFrom !== null && is_obejct($pmFrom))
		{
			$loActionFrom = self::getAction($pmFrom);
		}
		else
		{
			$loActionFrom = null;
		}
		
		$loToolbarBtn = self::getActionToolbar($pmButton, $loActionFrom);
		
		if ($loActionFrom !== null)
		{
			$loToolbarBtn = $loToolbarBtn->copy();
		}
		
		if ($pmTo !== null && is_string($pmTo))
		{
			$laResourceTo = explode("/", $pmTo);
			$loModuleTo = self::getModule($laResourceTo[0]);
			$loControllerTo = self::getController($laResourceTo[1], $loModuleTo);
			$loActionTo = self::getAction($laResourceTo[2], $loControllerTo);
		}
		elseif ($pmTo !== null && is_obejct($pmTo))
		{
			$loActionTo = self::getAction($pmTo);
		}
		else
		{
			$loActionTo = null;
		}
		
		$loActionTo->addToolbar($loToolbarBtn, $pnButtonNewIndice);
		
		return $loToolbarBtn;
	}

	public static function integrateActionColAction ($pmFrom, $pmTo, $pmButton, $pnButtonNewIndice = null)
	{
		if ($pmFrom !== null && is_string($pmFrom))
		{
			$laResourceFrom = explode("/", $pmFrom);
			$loModuleFrom = self::getModule($laResourceFrom[0]);
			$loControllerFrom = self::getController($laResourceFrom[1], $loModuleFrom);
			$loActionFrom = self::getAction($laResourceFrom[2], $loControllerFrom);
		}
		elseif ($pmFrom !== null && is_obejct($pmFrom))
		{
			$loActionFrom = self::getAction($pmFrom);
		}
		else
		{
			$loActionFrom = null;
		}
		
		$loColAction = self::getColAction($pmButton, $loActionFrom);
		
		if ($loActionFrom !== null)
		{
			$loColAction = $loColAction->copy();
		}
		
		if ($pmTo !== null && is_string($pmTo))
		{
			$laResourceTo = explode("/", $pmTo);
			$loModuleTo = self::getModule($laResourceTo[0]);
			$loControllerTo = self::getController($laResourceTo[1], $loModuleTo);
			$loActionTo = self::getAction($laResourceTo[2], $loControllerTo);
		}
		elseif ($pmTo !== null && is_obejct($pmTo))
		{
			$loActionTo = self::getAction($pmTo);
		}
		else
		{
			$loActionTo = null;
		}
		
		$loActionTo->addColAction($loColAction, $pnButtonNewIndice);
		
		return $loColAction;
	}

	public static function integrateColActionBtn ($pmFrom, $pmTo, $pmButton, $pnButtonNewIndice = null)
	{
		if ($pmFrom !== null && is_string($pmFrom))
		{
			$laResourceFrom = explode("/", $pmFrom);
			$loModuleFrom = self::getModule($laResourceFrom[0]);
			$loControllerFrom = self::getController($laResourceFrom[1], $loModuleFrom);
			$loActionFrom = self::getAction($laResourceFrom[2], $loControllerFrom);
			$loColActionFrom = self::getColAction($laResourceFrom[3], $loActionFrom);
		}
		elseif ($pmFrom !== null && is_obejct($pmFrom))
		{
			$loColActionFrom = self::getColAction($pmFrom);
		}
		else
		{
			$loColActionFrom = null;
		}
		
		$loColActionBtn = self::getColActionBtn($pmButton, $loColActionFrom);
		
		if ($loColActionFrom !== null)
		{
			$loColActionBtn = $loColActionBtn->copy();
		}
		
		if ($pmTo !== null && is_string($pmTo))
		{
			$laResourceTo = explode("/", $pmTo);
			$loModuleTo = self::getModule($laResourceTo[0]);
			$loControllerTo = self::getController($laResourceTo[1], $loModuleTo);
			$loActionTo = self::getAction($laResourceTo[2], $loControllerTo);
			$loColActionTo = self::getColAction($laResourceTo[3], $loActionTo);
		}
		elseif ($pmTo !== null && is_obejct($pmTo))
		{
			$loColActionTo = self::getColAction($pmTo);
		}
		else
		{
			$loColActionTo = null;
		}
		
		$loColActionTo->addColActionBtn($loColActionBtn, $pnButtonNewIndice);
		
		return $loColActionBtn;
	}
}