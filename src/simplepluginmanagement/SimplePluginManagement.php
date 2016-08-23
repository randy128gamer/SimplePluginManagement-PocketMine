<?php

namespace simplepluginmanagement;

use Exception;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class SimplePluginManagement extends PluginBase {
    
    public function onLoad() {
        $this->getLogger()->info("SimplePluginManagement is now loading...");
    }
    
    public function onEnable() {
        $this->getLogger()->info("SimplePluginManagement is now enabled.");
    }
    
    public function onDisable() {
        $this->getLogger()->info("SimplePluginManagement is now disabled.");
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        $noPermission = TextFormat::RED . "You don't have enough access to execute this command.";
        $usage = TextFormat::DARK_AQUA . "Usage: " . TextFormat::AQUA;
        switch ($command->getName()) {
            case "loadplugin":
                if ($sender->hasPermission("simplepluginmanagement.command.loaplugin")) {
                    if (count($args) <= 0) {
                        $sender->sendMessage($usage . "/loadplugin <filename>");
                        return false;
                    }
                    $rawFilename = "";
                    for ($i = 0; $i < count($args); $i++) {
                        $rawFilename .= $args[$i];
                        $rawFilename .= " ";
                    }
                    $filename = substr($rawFilename, 0, strlen($rawFilename) - 1);
                    if (!file_exists($filename)) {
                        $sender->sendMessage(TextFormat::GOLD . "Could not find the specific file.");
                        return false;
                    }
                    try {
                        $rawPlugin = $this->getServer()->getPluginManager()->loadPlugin($filename);
                        $this->getServer()->getPluginManager()->enablePlugin($rawPlugin);
                    } catch (Exception $ex) {
                        $sender->sendMessage(TextFormat::RED . "Please specify a valid plugin file.");
                        return false;
                    }
                    $sender->sendMessage(TextFormat::GREEN . "Plugin file " . TextFormat::AQUA . $filename . TextFormat::GREEN . " has been successfully loaded.");
                } else {
                    $sender->sendMessage($noPermission);
                }
                return true;
            case "enableplugin":
                if ($sender->hasPermission("simplepluginmanagement.command.enableplugin")) {
                    if (count($args) <= 0) {
                        $sender->sendMessage($usage . "/enableplugin <pluginname>");
                        return false;
                    }
                    $plugin = $this->getServer()->getPluginManager()->getPlugin($args[0]);
                    if ($plugin == null) {
                        $sender->sendMessage(TextFormat::GOLD . "Could not find the specifc file name.");
                        return false;
                    }
                    if ($plugin->isEnabled()) {
                        $sender->sendMessage(TextFormat::GOLD . "That plugin is already enabled.");
                        return false;
                    }
                    $this->getServer()->getPluginManager()->enablePlugin($plugin);
                    $sender->sendMessage(TextFormat::GREEN . "Plugin \"" . TextFormat::AQUA . $plugin->getName() . TextFormat::GREEN . "\" has been successfully enabled.");
                } else {
                    $sender->sendMessage($noPermission);
                }
                return true;
            case "disableplugin":
                if ($sender->hasPermission("simplepluginmanagement.command.disableplugin")) {
                    if (count($args) <= 0) {
                        $sender->sendMessage($usage . "/disableplugin <pluginname>");
                        return false;
                    }
                    $p = $this->getServer()->getPluginManager()->getPlugin($args[0]);
                    if ($p == null) {
                        $sender->sendMessage(TextFormat::GOLD . "Could not find the specific plugin.");
                        return false;
                    }
                    if ($p->isDisabled()) {
                        $sender->sendMessage(TextFormat::GOLD . "That plugin is already disabled.");
                        return false;
                    }
                    $this->getServer()->getPluginManager()->disablePlugin($p);
                    $sender->sendMessage(TextFormat::GREEN . "Plugin \"" . TextFormat::AQUA . $p->getName() . TextFormat::GREEN . "\" has been successfully disabled.");
                } else {
                    $sender->sendMessage($noPermission);
                }
                return true;
        }
        return false;
    }
}