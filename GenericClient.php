<?php
namespace plugin;
use phqagent\Server;
use phqagent\plugin\PluginBase;
use phqagent\console\MainLogger;
use phqagent\console\CommandManager;
use phqagent\console\TextFormat;
use phqagent\message\Message;

class GenericClient extends PluginBase{

    private $messagelist = [];

    public function onLoad(){
        CommandManager::register('list', $this);
        CommandManager::register('reply', $this);
        CommandManager::register('send', $this);
        MainLogger::success('交互客户端插件已加载');
    }

    public function onMessageReceive(Message $msg){
        $logstring = TextFormat::AQUA . '[' . $this->logMessage($msg) . ']'.
                     TextFormat::YELLOW . '[来源: ' . $msg->getFrom()->getName() . ']'.
                     TextFormat::PURPLE . '[发送者: ' . $msg->getSend()->getName() . '] '.
                     TextFormat::RESET . $msg->getContent();
        MainLogger::info($logstring);
    }

    public function onCommand(Server $server, $args){
        switch($args[0]){
            case 'reply':
                if(isset($args[1])){
                    if(isset($this->messagelist[$args[1]])){
                        if(isset($args[2])){
                            new Message($this->messagelist[$args[1]], $args[2], true);
                            MainLogger::success('消息已回复: [' . $args[1] . ']<<' . $args[2]);
                        }else{
                            MainLogger::warning('用法: reply [消息ID] [回复内容]');
                        }
                    }else{
                        MainLogger::warning('指定的消息ID不存在');
                    }
                }else{
                    MainLogger::warning('用法: reply [消息ID] [回复内容]');
                }
                break;
        }
    }

    private function logMessage(Message $msg){
        $this->messagelist[] = $msg;
        return count($this->messagelist) - 1;
    }    


}