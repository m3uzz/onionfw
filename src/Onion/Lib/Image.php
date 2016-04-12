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

namespace Onion\Lib;
use Onion\Log\Event;
use Onion\Log\Debug;


class Image
{
	public static $csCorFundo = '180,180,180';
	
	public static $cbEsticarImagem = true;
	
	public static $cbBorder = true;
	
	public static $caTipoImg = array("image/jpeg"=>"jpg",
			"image/pjpeg"=>"jpg",
			"image/png"=>"png",
			"image/gif"=>"gif",
			"application/x-shockwave-flash"=>"swf"
	);
	
	/**
	 * 
	 * @param string $psImagem
	 * @param string $psDestino
	 * @param int $pnLarguraMax
	 * @param int $pnAlturaMax
	 * @return boolean
	 */
	public static function gravarImagem($psImagem, $psDestino, $pnLarguraMax, $pnAlturaMax)
	{
		//Verificando se foi setada uma cor para preenchimento de fundo no config		
		if(defined('IMAGE_BGCOLOR'))
		{
			$lsCorFundo = IMAGE_BGCOLOR;
		}
		else //Senão utiliza a cor de fundo padrão da classe
		{
			$lsCorFundo = self::$csCorFundo;
		}
		
		//Verificando se foi setada a opção de esticar ou encolher imagem no config
		if(defined('IMAGE_STRETCH'))
		{
			$lbEsticarImagem = IMAGE_STRETCH;
		}
		else //Senão utiliza a definição padrão da classe
		{
			$lbEsticarImagem = self::$cbEsticarImagem;
		}
		
			//Verificando se foi setada a opção de esticar ou encolher imagem no config
		if(defined('IMAGE_BORDER'))
		{
			$lbBorder = IMAGE_BORDER;
		}
		else //Senão utiliza a definição padrão da classe
		{
			$lbBorder = self::$cbBorder;
		}
		
		Debug::debug("Cor de fundo: " . $lsCorFundo);
		Debug::debug("Esticar: " . $lbEsticarImagem);
		Debug::debug("Borda: " . $lbBorder);
		
		//Extraindo a extensão do nome da imagem
		$lsTipo = substr($psImagem, -3, 3);
		$lsTipo = strtolower($lsTipo);

		//Verificando qual o tipo da imagem e utilizando sua função específica de carregamento
		if($lsTipo == "jpg" || $lsTipo == "peg")
		{	
			$lrImg2 = ImageCreateFromJpeg($psImagem);
		}
		else if($lsTipo == "gif")
		{
			$lrImg2 = ImageCreateFromGif($psImagem);
		}
		
		if($lsTipo == "png")
		{
			$lrImg2 = ImageCreateFromPng($psImagem);
		}

		Debug::debug("Tipo: " . $lsTipo);
		
		//Se a imagem tiver sido carregada com sucesso, deve continuar o tratamento
		if(!empty($lrImg2))
		{
			//Recuperando as dimensões da imagem
			$lnAlturaReal = ImageSY($lrImg2);
			$lnLarguraReal = ImageSX($lrImg2);
			
			if($pnLarguraMax == "" || $pnLarguraMax == 0)
			{
				$lnLarguraMax = $lnLarguraReal;
			}
			else
			{
				$lnLarguraMax = $pnLarguraMax;
			}

			if($pnAlturaMax == "" || $pnAlturaMax == 0)
			{
				$lnAlturaMax = $lnAlturaReal;
			}
			else
			{
				$lnAlturaMax = $pnAlturaMax;
			}
				
			//Criando um handle para tratamento da imagem nas dimensões da mesma
			$lrImg = imagecreatetruecolor($lnLarguraMax,$lnAlturaMax);
			
			//Explodindo definições de cor de fundo para pegar separadamente cada camada
			$laCorFundo = explode(',',$lsCorFundo);

			//Inicializando novas dimenções da imagem a partir da dimensão atual
			$lnAlturaNova = $lnAlturaReal;
			$lnLarguraNova = $lnLarguraReal;

			if($lbBorder && !empty($pnAlturaMax) && !empty($pnLarguraMax))
			{
				Debug::debug("com borda");
				
				//Se a imagem puder ser esticada proporcionalmente
				if($lbEsticarImagem)
				{
					//Se a largura atual ou nova for menor que a largura Máxima ou ideal
					if($lnLarguraNova<$lnLarguraMax)
					{
						Debug::debug("esticando largura");
						
						//A altura deve aumentar proporcionalmente em relação a largura máxima ou ideal
						$lnAlturaNova = $lnLarguraMax *($lnAlturaNova/$lnLarguraNova);
						$lnLarguraNova=(int)$lnLarguraMax; //Aumenta para a largura máxima
						$lnAlturaNova = (int)$lnAlturaNova; //Aumenta para a altura máxima
					}
				}
	
				//Se a altura atual ou nova for maior que altura máxima
				if($lnAlturaNova>$lnAlturaMax)
				{
					Debug::debug("reduzindo altura");
						
					//A largura deve diminuir proporcionalmente em relação a altura máxima ou ideal
					$lnLarguraNova= $lnAlturaMax *($lnLarguraNova/$lnAlturaNova);
					$lnAlturaNova= (int)$lnAlturaMax; //Diminui para a altura máxima
					$lnLarguraNova= (int)$lnLarguraNova; //Diminui para nova largura
				}
	
				//Se a largura atual ou nova for maior que a largura máxima
				if($lnLarguraNova>$lnLarguraMax)
				{
					Debug::debug("reduzindo largura");
					
					//A altura deve diminuir proporcionalmente em relação a largura máxima ou ideal
					$lnAlturaNova = $lnLarguraMax *($lnAlturaNova/$lnLarguraNova);
					$lnLarguraNova=(int)$lnLarguraMax; //Diminui para a largura máxima
					$lnAlturaNova = (int)$lnAlturaNova; //Diminui para a nova altura
				}
			}
			else
			{
				Debug::debug("sem borda");
				Debug::debug($lnAlturaNova.">".$lnAlturaMax);
				
				//Se a altura atual ou nova for maior que altura máxima
				if($lnAlturaNova>$lnAlturaMax)
				{
					Debug::debug("reduzindo altura");
						
					//A largura deve diminuir proporcionalmente em relação a altura máxima ou ideal
					$lnLarguraNova= $lnAlturaMax *($lnLarguraNova/$lnAlturaNova);
					$lnAlturaNova= (int)$lnAlturaMax; //Diminui para a altura máxima
					$lnLarguraNova= (int)$lnLarguraNova; //Diminui para nova largura
				}
				
				Debug::debug($lnLarguraNova.">".$lnLarguraMax);
				
				//Se a largura atual ou nova for maior que a largura máxima
				if($lnLarguraNova>$lnLarguraMax)
				{
					Debug::debug("reduzindo largura");
						
					//A altura deve diminuir proporcionalmente em relação a largura máxima ou ideal
					$lnAlturaNova = $lnLarguraMax *($lnAlturaNova/$lnLarguraNova);
					$lnLarguraNova=(int)$lnLarguraMax; //Diminui para a largura máxima
					$lnAlturaNova = (int)$lnAlturaNova; //Diminui para a nova altura
				}

				//Se a imagem puder ser esticada proporcionalmente
				if($lbEsticarImagem)
				{
					//Se a largura atual ou nova for menor que a largura Máxima ou ideal
					if($lnLarguraNova<$lnLarguraMax)
					{
						Debug::debug("esticando largura");
						
						//A altura deve aumentar proporcionalmente em relação a largura máxima ou ideal
						$lnAlturaNova = $lnLarguraMax *($lnAlturaNova/$lnLarguraNova);
						$lnLarguraNova=(int)$lnLarguraMax; //Aumenta para a largura máxima
						$lnAlturaNova = (int)$lnAlturaNova; //Aumenta para a altura máxima
					}
					
					//Se a altura atual ou nova for menor que a altura Máxima ou ideal
					if($lnAlturaNova<$lnAlturaMax)
					{
						Debug::debug("esticando altura");
						
						//A largura deve aumentar proporcionalmente em relação a altura máxima ou ideal
						$lnLarguraNova = $lnAlturaMax *($lnLarguraNova/$lnAlturaNova);
						$lnAlturaNova= (int)$lnAlturaMax; //Aumenta para a altura máxima
						$lnLarguraNova= (int)$lnLarguraNova; //Aumenta para nova largura
					}
				}
				
			}
			
			//Centralizando imagem dentro do frame
			$lnPosX = ($lnLarguraMax-$lnLarguraNova)/2;
			$lnPosY = ($lnAlturaMax-$lnAlturaNova)/2;
			
			Debug::debug("Dimensão Original: ".$lnLarguraReal."x".$lnAlturaReal);
			Debug::debug("Dimensão Nova: ".$lnLarguraNova."x".$lnAlturaNova);
			
			if($lsTipo == "png")
			{				
				Debug::debug("criando png");
				
				//Ativa a camada de transparência para a imagem 
				imagealphablending($lrImg, true);
									
				//Aloca a cor transparente e preenche a nova imagem com isto.
				//Sem isto a imagem vai ficar com fundo preto ao invés de transparente.
				$loTransparent = imagecolorallocatealpha( $lrImg, 0, 0, 0, 127 );
				imagefill( $lrImg, 0, 0, $loTransparent );					

				//copia o frame na imagem final
				imagecopyresampled ($lrImg, $lrImg2, $lnPosX, $lnPosY, 0, 0, $lnLarguraNova, $lnAlturaNova, $lnLarguraReal, $lnAlturaReal);					
				imagealphablending($lrImg, false);					

				//Salva a camada alpha (transparência)
				imagesavealpha($lrImg,true);				
			}
			elseif($lsTipo == "gif")
			{
				Debug::debug("criando gif");
				
				//redimencionando a imagem e criando a imagem final
				imagecopyresized ($lrImg, $lrImg2, $lnPosX, $lnPosY, 0, 0, $lnLarguraNova, $lnAlturaNova, $lnLarguraReal, $lnAlturaReal);
			}
			else
			{
				Debug::debug("criando jpg");
				//definindo a cor de fundo da imagem e preenchendo a camada
				$lrFundo = imagecolorallocate($lrImg, $laCorFundo[0],$laCorFundo[1],$laCorFundo[2]);				
				imagefill($lrImg, 0, 0, $lrFundo);
				
				//redimencionando a imagem e criando a imagem final
				imagecopyresized ($lrImg, $lrImg2, $lnPosX, $lnPosY, 0, 0, $lnLarguraNova, $lnAlturaNova, $lnLarguraReal, $lnAlturaReal);
			}

			if($lsTipo == "jpg" || $lsTipo == "peg")
			{
				//header("Content-Type: image/jpeg");
				if(!imagejpeg ($lrImg,$psDestino)) //gravando imagem JPG no destino
				{
					Event::log(array(
						"userId" => null,
						"class" => "Image",
						"method" => "gravarImagem",
						"msg" => "Create image JPG failed!"
					), Event::ERR);
					
					Debug::debug("Create image JPG failed!");
				}
			}
			else if($lsTipo == "gif")
			{
				//header("Content-Type: image/gif");
				if(!imagegif ($lrImg,$psDestino)) //gravando imagem GIF no destino
				{
					Event::log(array(
						"userId" => null,
						"class" => "Image",
						"method" => "gravarImagem",
						"msg" => "Create image GIF failed!"
					), Event::ERR);
					
					Debug::debug("Create image GIF failed!");
				}
			}
			else if($lsTipo == "png")
			{
				//header("Content-Type: image/png");
				if(!imagepng ($lrImg,$psDestino)) //gravando imagem PNG no destino
				{
					Event::log(array(
						"userId" => null,
						"class" => "Image",
						"method" => "gravarImagem",
						"msg" => "Create image PNG failed!"
					), Event::ERR);
					
					Debug::debug("Create image PNG failed!");
				}
			}
				
			imagedestroy($lrImg);
			return true;
		}
		else
		{
			Event::log(array(
				"userId" => null,
				"class" => "Image",
				"method" => "gravarImagem",
				"msg" => "Image truncated!"
			), Event::ERR);
			
			Debug::debug("Image truncated!");
			return false;
		}
	}
}
?>