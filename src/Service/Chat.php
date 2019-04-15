<?php
namespace App\Service;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Chat implements MessageComponentInterface
{
    private $users = [];
    private $botName = 'ChatBot';
    private $defaultChannel = 'general';
    private $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->users[$conn->resourceId] = [
            'connection' => $conn,
            'user' => '',
            'channels' => []
        ];

        $this->clients->attach($conn);
        $conn->send(sprintf('New connection: Hello #%d', $conn->resourceId));
    }

    public function onClose(ConnectionInterface $closedConnection)
    {
        unset($this->users[$closedConnection->resourceId]);
        
        $this->clients->detach($closedConnection);
        echo sprintf('Connection #%d has disconnected\n', $closedConnection->resourceId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send('An error has occurred: '.$e->getMessage());
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $message)
    {
        $totalClients = count($this->clients) - 1;
        echo vsprintf(
            'Connection #%1$d sending message "%2$s" to %3$d other connection%4$s'."\n", [
            $from->resourceId,
            $message,
            $totalClients,
            $totalClients === 1 ? '' : 's'
        ]);
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($message);
            }
        }
    }

    private function subscribeToChannel(ConnectionInterface $conn, $channel, $user)
    {
        $this->users[$conn->resourceId]['channels'][$channel] = $channel;
        $this->sendMessageToChannel(
            $conn,
            $channel,
            $this->botName,
            $user.' joined #'.$channel
        );
    }

    private function unsubscribeFromChannel(ConnectionInterface $conn, $channel, $user)
    {
        if (array_key_exists($channel, $this->users[$conn->resourceId]['channels'])) {
            unset($this->users[$conn->resourceId]['channels']);
        }
        $this->sendMessageToChannel(
            $conn,
            $channel,
            $this->botName,
            $user.' left #'.$channel
        );
    }

    private function sendMessageToChannel(ConnectionInterface $conn, $channel, $user, $message)
    {
        if (!isset($this->users[$conn->resourceId]['channels'][$channel])) {
            return false;
        }
    foreach ($this->users as $connectionId => $userConnection) {
            if (array_key_exists($channel, $userConnection['channels'])) {
                $userConnection['connection']->send(json_encode([
                    'action' => 'message',
                    'channel' => $channel,
                    'user' => $user,
                    'message' => $message
                ]));
            }
        }
        return true;
    }
}