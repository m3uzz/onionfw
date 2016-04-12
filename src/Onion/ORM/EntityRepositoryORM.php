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

namespace Onion\ORM;
use \Doctrine\ORM\EntityRepository;
use \Doctrine\Common\Cache\ApcCache;
use Onion\Config\Config;
use Onion\Log\Debug;
use Onion\Lib\String;

class EntityRepositoryORM extends EntityRepository
{
	
	public function getList ($paParams, $pbCache = false)
	{		
		$paParams['col'] = String::escapeString($paParams['col']);
		$paParams['ord'] = String::escapeString($paParams['ord']);
		$paParams['page'] = String::escapeString($paParams['page']);
		$paParams['rows'] = String::escapeString($paParams['rows']);
	
		$loQB = $this->getEntityManager()->createQueryBuilder();
		
		$loQB->from("{$this->_entityName}", 'a');
		
		$loQB->where("1=1");
		
		if (isset($paParams['status']) && is_numeric($paParams['status']))
		{
			$loQB->andWhere("a.numStatus = {$paParams['status']}");
		}
		
		if (isset($paParams['active']) && is_numeric($paParams['active']))
		{
			$loQB->andWhere("a.isActive = '{$paParams['active']}'");
		}
		
		if (isset($paParams['where']))
		{
			if (is_array($paParams['where']))
			{
				foreach ($paParams['where'] as $lsWhere)
				{
					if (!empty($lsWhere))
					{
						$loQB->andWhere("{$lsWhere}");
					}
				}
			}
			else if (!empty($paParams['where']))
			{
				$loQB->andWhere("{$paParams['where']}");
			}
		}
		
		$loQB->select("count('a') AS qt");
		$loQB->distinct();
		//Debug::display($loQB->getDql());
		$loQueryPaginator = $loQB->getQuery();
		
		$loQB->select('a');
		
		$lsOrderField = $paParams['col'];
		
		if (!preg_match("/^[a-z]+\./", $lsOrderField))
		{
			$lsOrderField = "a.{$lsOrderField}";
		}
		 
		$loQB->orderBy("{$lsOrderField}", "{$paParams['ord']}");
	
		//Debug::display($loQB->getDql());
	
		$loQuery = $loQB->getQuery();
	
		if ($paParams['rows'] > 0)
		{
			$loQuery->setFirstResult($paParams['page']);
			$loQuery->setMaxResults($paParams['rows']);
		}
	
		if (!$pbCache)
		{
			$loQuery->expireResultCache(true);
		}
		else
		{
			$loQuery->setResultCacheDriver(new ApcCache());
			$loQuery->useResultCache(true);
			$loQuery->setResultCacheLifeTime(3600);
		}

		$laObjectResults = $loQuery->getResult();
		$laArrayResults = null;
		$lnTotalCount = 0;
		
		if (is_array($laObjectResults))
		{
			foreach ($laObjectResults as $loRes)
			{
				$laArrayResults[] = $loRes->getFormatedData();
				$lnTotalCount++;
			}
		}
		
		if ($paParams['rows'] > 0)
		{
			$laPaginatorResult = $loQueryPaginator->getResult();
				
			if (isset($laPaginatorResult[0]['qt']))
			{
				$lnTotalCount = $laPaginatorResult[0]['qt'];
			}
		}
	
		return array(
			'resultSet' => $laArrayResults,
			'totalCount' => $lnTotalCount
		);
	}
	
	public function search ($paParams, $pbCache = false)
	{
		$paParams['col'] = String::escapeString($paParams['col']);
		$paParams['ord'] = String::escapeString($paParams['ord']);
		$paParams['rows'] = String::escapeString($paParams['rows']);
	
		$loQB = $this->getEntityManager()->createQueryBuilder();
	
		$loQB->from("{$this->_entityName}", 'a');
		$loQB->where("a.numStatus = {$paParams['status']}");
		$loQB->andWhere("a.isActive = '{$paParams['active']}'");
	
		if (isset($paParams['where']))
		{
			if (is_array($paParams['where']))
			{
				foreach ($paParams['where'] as $lsWhere)
				{
					if (!empty($lsWhere))
					{
						$loQB->andWhere("{$lsWhere}");
					}
				}
			}
			else if (!empty($paParams['where']))
			{
				$loQB->andWhere("{$paParams['where']}");
			}
		}
	
		$loQB->distinct();

		$loQB->select('a');
	
		$lsOrderField = $paParams['col'];
	
		if (!preg_match("/^[a-z]+\./", $lsOrderField))
		{
			$lsOrderField = "a.{$lsOrderField}";
		}
			
		$loQB->orderBy("{$lsOrderField}", "{$paParams['ord']}");
	
		//Debug::display($loQB->getDql());
	
		$loQuery = $loQB->getQuery();
	
		if ($paParams['rows'] > 0)
		{
			$loQuery->setMaxResults($paParams['rows']);
		}
	
		if (!$pbCache)
		{
			$loQuery->expireResultCache(true);
		}
		else
		{
			$loQuery->setResultCacheDriver(new ApcCache());
			$loQuery->useResultCache(true);
			$loQuery->setResultCacheLifeTime(3600);
		}
	
		$laObjectResults = $loQuery->getResult();
		$laArrayResults = null;
		$lnTotalCount = 0;
	
		if (is_array($laObjectResults))
		{
			foreach ($laObjectResults as $loRes)
			{
				$laArrayResults[] = $loRes->getFormatedData();
				$lnTotalCount++;
			}
		}

		return array(
			'resultSet' => $laArrayResults,
			'totalCount' => $lnTotalCount
		);
	}
}