<?php

/*
 * LegionPE Theta
 *
 * Copyright (C) 2015 PEMapModder and contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace legionpe\theta\command;

use legionpe\theta\lang\Phrases;
use legionpe\theta\Session;
use pocketmine\command\CommandSender;
use pocketmine\event\TextContainer;
use pocketmine\Player;

abstract class SessionCommand extends ThetaCommand{
	public function testPermissionSilent(CommandSender $sender){
		if(!($sender instanceof Player)){
			return false;
		}
		$session = $this->getPlugin()->getSession($sender);
		if(!($session instanceof Session)){
			return false;
		}
		return $this->checkPerm($session);
	}
	/**
	 * @param Session $session
	 * @param string $msg
	 * @return bool
	 */
	protected function checkPerm(/** @noinspection PhpUnusedParameterInspection */
		Session $session, &$msg = null){
		return true;
	}
	public function testPermission(CommandSender $sender){
		if(!($sender instanceof Player)){
			$sender->sendMessage(Phrases::VAR_error . "Please run this command in-game.");
			return false;
		}
		$session = $this->getPlugin()->getSession($sender);
		if(!($session instanceof Session)){
			$sender->sendMessage(Phrases::VAR_wait . "Please run this command later. We are still preparing your account. Sorry for the inconvenience.");
			return false;
		}
		if(!$this->checkPerm($session, $msg)){
			$sender->sendMessage(Phrases::VAR_error . ($msg === null ? $session->translate(Phrases::CMD_ERR_NO_PERM) : $msg));
			return false;
		}
		return true;
	}
	public function execute(CommandSender $sender, $l, array $args){
		if(!($sender instanceof Player)){
			$sender->sendMessage(Phrases::VAR_error . "Please run this command in-game.");
			return true;
		}
		$session = $this->getPlugin()->getSession($sender);
		if(!($session instanceof Session)){
			return true;
		}
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$r = $this->run($args, $session, $l);
		if($r === false){
			$session->send(Phrases::CMD_ERR_WRONG_USE, ["usage" => $this->getUsage()]);
		}elseif(is_string($r) or ($r instanceof TextContainer)){
			$sender->sendMessage($r);
		}
		return true;
	}
	protected abstract function run(array $args, Session $sender);
	protected function offline(Session $sender, $name){
		return $sender->translate(Phrases::CMD_ERR_ABSENT_PLAYER_NAME_KNOWN, ["player" => $name]);
	}
}
