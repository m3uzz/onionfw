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
use Onion\Lib\Session;
use Onion\Config\Config;

class Register
{

	/**
	 * Carrega e registra todos os módulos abilitados para o sistema.
	 */
	public static function getModules ()
	{
		$laConfig = Config::getAppOptions('modules');
		
		$laModules = isset($laConfig['available']) ? $laConfig['available'] : null;
		$laApp = isset($laConfig['appSections']) ? $laConfig['appSections'] : null;
		$laSection = array();
		
		if (is_array($laApp))
		{
			foreach ($laApp as $lsSection => $laValue)
			{
				if (is_array($laValue['modules']))
				{
					foreach ($laValue['modules'] as $lsModule => $lsV)
					{
						$laSection[$lsModule] = $lsSection;
					}
				}
			}
		}
		
		if (is_array($laModules))
		{
			foreach ($laModules as $lsModule => $lbIsActive)
			{
				if ($lbIsActive)
				{
					$lsSection = "[%SECTION%]";
					
					if (isset($laSection[$lsModule]))
					{
						$lsSection = $laSection[$lsModule];
					}
					
					$lsModSetDefaultPath = BASE_DIR . DS . 'module' . DS . $lsModule . DS . 'config' . DS . 'settings.php';
					$lsModSetClientPath = CLIENT_DIR . DS . 'module' . DS . $lsModule . DS . 'config' . DS . 'settings.php';
					
					if (file_exists($lsModSetDefaultPath))
					{
						include ($lsModSetDefaultPath);
					}
					
					if (file_exists($lsModSetClientPath))
					{
						include ($lsModSetClientPath);
					}
					
					$laModuleRegistered[$lsModule] = $lsModSetDefaultPath;
				}
			}
		}
		
		Session::setRegister('moduleRegistered', $laModuleRegistered);
	}

	/**
	 *
	 * @param boolean $pbInclude        	
	 * @return array
	 */
	public static function getExtendedModules ()
	{
		$laModuleRegistered = Session::getRegister("moduleRegistered");
		
		$laConfig = Config::getAppOptions('modules');
		
		$laModules = isset($laConfig['extended']) ? $laConfig['extended'] : null;
		
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
					
					$laModuleRegistered[$lsModule] = $lsModule . ".php";
				}
			}
		}
		
		Session::setRegister('moduleRegistered', $laModuleRegistered);
	}

	public static function registreResource (Module $poModule)
	{
		$loResource = Session::getRegister("resource");
		
		$loResource[$poModule->get('id')] = $poModule;
		
		Session::setRegister('resource', $loResource);
	}

	/**
	 *
	 * @param string $pmResource        	
	 * @param string $pbValid        	
	 * @throws Exception
	 * @return NULL unknown
	 */
	public static function getResources ($pmResource = null, $pbValid = true)
	{
		$laResources = Session::getRegister("resource");
		
		if ($pmResource !== null && isset($laResources[$pmResource]))
		{
			return $laResources[$pmResource];
		}
		elseif ($pmResource !== null && is_string($pmResource))
		{
			if (is_array($laResources))
			{
				foreach ($laResources as $lnKey => $loObj)
				{
					if (is_object($loObj) && $loObj->_id == $pmResource)
					{
						return $loObj;
					}
				}
			}
			
			if ($pbValid)
			{
				throw new Exception('O modulo "' . $pmResource . '" não existe! [2]');
			}
		}
		elseif ($pmResource === null)
		{
			return $laResources;
		}
		elseif ($pbValid)
		{
			throw new Exception('O modulo "' . $pmResource . '" não existe! [1]');
		}
		
		return null;
	}

	/**
	 *
	 * @param unknown $psModule        	
	 */
	public static function getModuleHash ($psModule)
	{
		$poModule = self::getResources($psModule);
		return $poModule->get('hash');
	}

	/**
	 * Retora uma lista de recursos e suas definições para aplicação das regras
	 * de ACL.
	 *
	 * @return array
	 */
	public static function getResourcesList ()
	{
		$laResources = self::getResources(); // recuperando a arvore de recursos
		                                     // registrados na sessão.
		$laRes = array(); // inicializando o array que será montado com os dados
		                  // de retorno.
		
		if (is_array($laResources)) // veriricando se a variável é um array valido
		                           // para ser percorrido
		{
			foreach ($laResources as $loResource) // percorrendo o array de recursos
			                                     // para tratar um a um
			{
				// if($loResource->get('enable')) //verificando se o recurso
				// está habilitado para o sistema
				// {
				$lsResourceId = $loResource->get('id'); // carregando o valor da
				                                        // propriedade id
				$lsResource = $loResource->get('resource'); // carregando o valor da
				                                            // propriedade resource
				$lsResourceName = $loResource->get('name'); // carregando o valor da
				                                            // propriedade name
				$laControllers = $loResource->get('controller'); // carregando a
				                                                 // lista de controllers
				                                                 // registrados no módulo
				
				if (is_array($laControllers) && count($laControllers) != 0) // verificando
				                                                           // se a lista de
				                                                           // controllers é
				                                                           // um array
				                                                           // válido
				{
					foreach ($laControllers as $loController) // percorrendo a lista
					                                         // de controllers para tratar um a
					                                         // um
					{
						$laActions = $loController->get('action'); // carregando a
						                                           // lista de actions registradas
						                                           // no controller
						
						if (is_array($laActions)) // verificando se a lista de actions
						                         // é um array válido
						{
							foreach ($laActions as $lsAction => $loAction) // percorrendo
							                                              // a lista de actions para
							                                              // tratar um a um
							{
								// criando o indice para o array, utilizando
								// seus elementos para obtenham um indice único
								$lsX = preg_replace("/:/", "-", $lsResource) . "-" . $loController->get('id') . "-" . $loAction->get('id');
								
								$laRes[$lsX]['active'] = ""; // indicará se a regra
								                                // será aplicada (true) ou poderá ser
								                                // sobrescrita pela herança
								$laRes[$lsX]['resource'] = $lsResource; // indica o
								                                          // identificador do recurso,
								                                          // composto de
								                                          // module:controller
								$laRes[$lsX]['resourceName'] = $lsResourceName; // indica
								                                                 // o nome do recurso,
								                                                 // nome do módulo para
								                                                 // exibir na tela de
								                                                 // configuração de
								                                                 // regras
								$laRes[$lsX]['module'] = $lsResourceId; // indica o
								                                           // identificador do module
								$laRes[$lsX]['controller'] = $loController->get('id'); // indica
								                                                         // o
								                                                         // identificador
								                                                         // do
								                                                         // controller
								$laRes[$lsX]['controllerName'] = $loController->get('name'); // indica
								                                                             // o nome
								                                                             // do
								                                                             // controller
								$laRes[$lsX]['privilege'] = $loAction->get('id'); // indica
								                                                    // o identificador a
								                                                    // action que será o
								                                                    // privilégio
								$laRes[$lsX]['privilegeName'] = $loAction->get('name'); // indica
								                                                         // o nome da
								                                                         // action ou
								                                                         // privilégio a
								                                                         // ser exibido
								                                                         // na tela de
								                                                         // configuração
								                                                         // de regras
								$laRes[$lsX]['description'] = $loAction->get('description'); // carrega
								                                                              // a
								                                                              // descrição
								                                                              // da
								                                                              // action
								                                                              // para
								                                                              // ser
								                                                              // exibida
								                                                              // na tela
								                                                              // de
								                                                              // configuração
								                                                              // de
								                                                              // regras
								$laRes[$lsX]['assert'] = ""; // uso indefinido TODO:
								                                // definir uso do assert
								$laRes[$lsX]['allow'] = ""; // define se a permissão
								                               // ou negação do privilério, onde true é
								                               // permitido e false negado, porem
								                               // dependendo do item enable
								$laRes[$lsX]['heritage'] = null; // indica o valor de
								                                   // allow do seu pai, caso necessário
								                                   // recuperar a herança
								
								$lsActionResource = $loAction->get('resource'); // recuperando
								                                                // o valor da
								                                                // propriedade resource
								                                                // da action
								
								if (! empty($lsActionResource)) // verificando se a
								                              // propriedade resource da action está
								                              // setada
								{
									$laRes[$lsX]['resource'] = $lsActionResource; // alterando
									                                              // o valor de resource,
									                                              // utilizando o resource
									                                              // da action
								}
							}
						}
					}
				}
				else
				{
					// criando o indice para o array, utilizando seus elementos
					// para obtenham um indice único
					$lsX = $lsResource;
					
					$laRes[$lsX]['active'] = ""; // indicará se a regra será
					                                // aplicada (true) ou poderá ser
					                                // sobrescrita pela herança
					$laRes[$lsX]['resource'] = $lsResource; // indica o
					                                          // identificador do recurso,
					                                          // composto de module:controller
					$laRes[$lsX]['resourceName'] = $lsResourceName; // indica o nome
					                                                 // do recurso, nome do
					                                                 // módulo para exibir na
					                                                 // tela de configuração de
					                                                 // regras
					$laRes[$lsX]['module'] = $lsResourceId; // indica o
					                                           // identificador do module
					$laRes[$lsX]['controller'] = ""; // indica o identificador do
					                                   // controller
					$laRes[$lsX]['controllerName'] = ""; // indica o nome do
					                                     // controller
					$laRes[$lsX]['privilege'] = null; // indica o identificador a
					                                    // action que será o privilégio
					$laRes[$lsX]['privilegeName'] = ""; // indica o nome da action
					                                     // ou privilégio a ser exibido na tela
					                                     // de configuração de regras
					$laRes[$lsX]['description'] = ""; // carrega a descrição da
					                                   // action para ser exibida na tela de
					                                   // configuração de regras
					$laRes[$lsX]['assert'] = ""; // uso indefinido TODO: definir
					                                // uso do assert
					$laRes[$lsX]['allow'] = ""; // define se a permissão ou
					                               // negação do privilério, onde true é
					                               // permitido e false negado, porem
					                               // dependendo do item enable
					$laRes[$lsX]['heritage'] = null; // indica o valor de allow do
					                                   // seu pai, caso necessário recuperar a
					                                   // herança
				}
				// }
			}
		}
		
		return $laRes; // retornando o array com a lista de recursos e suas
		               // propriedades para serem aplicadas no ACL
	}

	/**
	 * Carrega a lista de módulos registrados na sessão
	 *
	 * @return array
	 */
	public static function getModuleRegistered ()
	{
		$laResources = Session::getRegister("moduleRegistered");
		return $laResources;
	}

	/**
	 * Remove um módulo da lista de módulos registrados, caso ele exista na
	 * mesma
	 *
	 * @param string $psModule
	 *        	- Nome do indice para recuperação do módulo desejado
	 */
	public static function removeResouce ($psModule)
	{
		$loResource = Session::getRegister("resource");
		
		if (isset($psModule))
		{
			unset($loResource[$psModule]);
			Session::setRegister('resource', $loResource);
		}
	}

	public static function clearResources ()
	{
		$loResource = Session::getRegister("resource");
		
		$loResource = null;
		Session::setRegister('resource', $loResource);
	}

	public static function isResource ($psModule)
	{
		$laResources = Session::getRegister("resource");
		
		if (isset($laResources[$psModule]))
		{
			return true;
		}
		
		return false;
	}

	public static function getColAction ($psModule, $psController, $psAction, $pnId, $paParams = null)
	{
		$loResource = Register::getResources($psModule);
		$loController = $loResource->getController($psController);
		$loAction = $loController->getAction($psAction);
		$laColActions = $loAction->get('colAction');
		$laActions = array();
		$lsParams = $pnId;
		
		if (is_array($paParams))
		{
			foreach ($paParams as $lsVar => $lsValue)
			{
				$lsParams .= '/' . $lsVar . '/' . $lsValue;
			}
		}
		
		if (is_array($laColActions))
		{
			foreach ($laColActions as $lnKey => $loColAction)
			{
				$laColAction = is_object($loColAction) ? $loColAction->legacy($lsParams) : $loColAction;
				
				if (is_array($laColAction))
				{
					$laActions[$lnKey] = $laColAction;
				}
			}
		}
		return $laActions;
	}

	public static function registreApplication ()
	{
		$loApp = Session::getRegister("app");
		
		$laSections = Config::getAppOptions('modules');
		
		if (is_array($laSections['app']))
		{
			$laResources = self::getResources();
			
			foreach ($laSections['app'] as $lsSection => $laValue)
			{
				$laApp[$lsSection] = new Section($lsSection);
				$laApp[$lsSection]->set(array(
					'description' => '',
					'title' => $laValue['name'],
					'icon' => $laValue['icon']
				));
				
				if (is_array($laValue['modules']))
				{
					foreach ($laValue['modules'] as $lsModule => $lsV)
					{
						if (isset($laResources[$lsModule]))
						{
							if (isset($lsV['name']) && ! empty($lsV['name']))
							{
								$laResources[$lsModule]->setName($lsV['name']);
							}
							
							$laApp[$lsSection]->addModule($laResources[$lsModule]);
						}
					}
				}
			}
		}
		
		Session::setRegister('app', $laApp);
	}

	public static function getApplication ()
	{
		$loApp = Session::getRegister("app");
		return $loApp;
	}

	public static function replaceSectionIntegration ()
	{
		$laApp = self::getApplication();
		
		if (is_array($laApp))
		{
			foreach ($laApp as $lsSection => $loApp)
			{
				if (is_object($loApp))
				{
					$loApp->replaceSection();
				}
			}
		}
	}

	public static function getApplicationArray ($pbLegacy = false)
	{
		$laApp = self::getApplication();
		
		if (is_array($laApp))
		{
			foreach ($laApp as $lsSection => $loApp)
			{
				if ($pbLegacy)
				{
					$laSection = is_object($loApp) ? $loApp->legacy() : $loApp;
					
					if ($laSection != null)
					{
						$laApps[$lsSection] = $laSection;
					}
				}
				else
				{
					$laApps[$lsSection] = is_object($loApp) ? $loApp->get() : $loApp;
				}
			}
		}
		
		return $laApps;
	}

	public static function removeApplication ($psModule)
	{
		$loApp = Session::getRegister("app");
		
		if (isset($psModule))
		{
			unset($loApp[$psModule]);
			Session::setRegister('app', $loApp);
		}
	}

	public static function clearSection ()
	{
		$loResource = Session::getRegister("app");
		
		$loResource = null;
		Session::setRegister('app', $loResource);
	}

	public static function getActiveCategory ()
	{
		$laResources = Config::getAppOptions('category');
		
		if (isset($laResources['category_mod']))
		{
			return $laResources['category_mod'];
		}
		
		return array();
	}

	public static function getNameCategory ($psKey)
	{
		$laCaregorias = self::getActiveCategory();
		
		if (is_array($laCaregorias) && isset($laCaregorias[$psKey]))
		{
			return $laCaregorias[$psKey];
		}
	}

	public static function getActiveStatus ()
	{
		$laResources = Config::getAppOptions('status');
		
		if (isset($laResources['table']))
		{
			return $laResources['table'];
		}
		
		return array();
	}

	public static function getDefaultFieldValue ($psTable, $psSection)
	{
		$laConfig = Config::getAppOptions('table');
		
		if (isset($laConfig[$psTable]))
		{
			return $laConfig[$psTable];
		}
		
		return null;
	}

	/**
	 * Carrega o valor padrão para o campo numStatus de uma determinada tabela.
	 * O valor é setado no table.ini da aplicação e no config do cliente, caso
	 * tenha uma regra específica.
	 *
	 * @param string $psTable
	 *        	- nome da table que se deseja buscar o valor padrão para o
	 *        	campo numStatus da table
	 * @return int NULL
	 */
	public static function getStatusDefault ($psTable)
	{
		return self::getDefaultFieldValue($psTable, 'table_status');
	}

	/**
	 * Carrega o valor padrão para o campo isActive de uma determinada tabela.
	 * O valor é setado no table.ini da aplicação e no config do cliente, caso
	 * tenha uma regra específica.
	 *
	 * @param string $psTable
	 *        	- nome da table que se deseja buscar o valor padrão para o
	 *        	campo isActive da table
	 * @return int NULL
	 */
	public static function getActiveDefault ($psTable)
	{
		return self::getDefaultFieldValue($psTable, 'table_active');
	}

	/**
	 * Verifica se o objeto tem permissão de exibição
	 *
	 * @param array $paRoles        	
	 * @param string $psLevel        	
	 *
	 * @return boolean
	 */
	public static function visible ($paRoles)
	{
		$laResources = self::getApplication();
		
		if (is_array($laResources))
		{
			foreach ($laResources as $loResource)
			{
				$loResource->visible($paRoles);
			}
		}
	}
}