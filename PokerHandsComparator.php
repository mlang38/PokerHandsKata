<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FTH\Katas\PokerHands;

use InvalidArgumentException;

/**
 * Description of PokerHandsComparator
 *
 * @author mlangevin
 */
class PokerHandsComparator
{

    private $handPattern = '(\w+)\s*:\s*([23456789TJQKA][CDHS])\s+((?2))\s+((?2))\s+((?2))\s+((?2))(?(?=.)\s+)';
    private $values = ['2', '3', '4', '5', '6', '7', '8', '9', 'T', 'J', 'Q', 'K', 'A'];

    public function whoWins($pokerHands)
    {
        $hands = $this->parseHandsString($pokerHands);

        // Highest card
        $allCards = $this->sortCardsByValue($this->getAllCards($hands));
        $highestCard = $this->getHighestCard($allCards);
        $player = $this->findPlayerWithThatCard($highestCard, $hands);

        return "{$player} wins - high card : {$highestCard}";
    }

    private function parseHandsString($pokerHandsString)
    {
        // Malformed ?
        if (!\preg_match('/^(?:' . $this->handPattern . '){2}$/', $pokerHandsString)) {
            throw new InvalidArgumentException;
        }

        // Captures matches
        $matches = [];
        \preg_match_all("/{$this->handPattern}/", $pokerHandsString, $matches, PREG_SET_ORDER);

        // Build $hands
        $hands = [];
        foreach ($matches as $hand) {
            $hands[$hand[1]] = \array_slice($hand, 2);
        }

        // Duplicate card ?
        if (\count(\call_user_func_array('array_intersect', $hands))) {
            throw new InvalidArgumentException;
        }

        return $hands;
    }

    private function getAllCards($hands)
    {
        return \call_user_func_array('array_merge', $hands);
    }

    private function sortCardsByValue($cards)
    {
        \usort($cards, [$this, 'compareCardsByValues']);
        return \array_reverse($cards);
    }

    private function compareCardsByValues($card1, $card2)
    {
        $value1 = \array_search($card1[0], $this->values);
        $value2 = \array_search($card2[0], $this->values);
        if ($value1 === $value2) {
            return 0;
        }
        return ($value1 > $value2) ? 1 : -1;
    }

    private function findPlayerWithThatCard($card, $hands)
    {
        $playerFound = null;
        foreach ($hands as $player => $hand) {
            if (false !== \array_search($card, $hand)) {
                $playerFound = $player;
                break;
            }
        }
        return $playerFound;
    }

    private function getHighestCard($cards)
    {
        $highestCard = null;
        for ($i = 0, $count = count($cards) - 1; $i < $count; $i++) {
            if ($cards[$i][0] !== $cards[$i + 1][0]) {
                $highestCard = $cards[$i];
                break;
            }
            $i++;
        }
        return $highestCard;
    }

}
