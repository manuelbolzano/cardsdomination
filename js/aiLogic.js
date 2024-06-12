import { playCard } from './game.js';

export function aiTurn() {
    console.log("AI's turn");
    let availableCells = board.map((cell, index) => cell === null ? index : null).filter(cell => cell !== null);
    let aiCardIndex = Math.floor(Math.random() * aiCards.length);
    let aiCard = aiCards[aiCardIndex];
    let maxScoreIncrease = -Infinity;
    let bestMove = null;

    // Cerca la migliore mossa per l'AI
    availableCells.forEach(cellId => {
        let potentialScore = simulatePlay(aiCard.id, cellId, 'ai');
        if (potentialScore > maxScoreIncrease) {
            maxScoreIncrease = potentialScore;
            bestMove = cellId;
        }
    });

    if (bestMove !== null) {
        playCard(aiCard.id, bestMove, 'ai');
    }
}

function simulatePlay(cardId, cellId, side) {
    let tempBoard = board.slice();
    let card = getCardById(cardId, side);
    tempBoard[cellId] = { card: card, side: side };
    let scoreIncrease = calculateScoreIncrease(tempBoard, cellId, side);
    return scoreIncrease;
}

function calculateScoreIncrease(board, cellId, side) {
    let scoreIncrease = 0;
    let adjacentIndices = getAdjacentIndices(cellId);

    adjacentIndices.forEach(index => {
        if (board[index] && board[index].side !== side) {
            let currentCard = board[cellId].card;
            let adjacentCard = board[index].card;
            if (compareCards(currentCard, adjacentCard, cellId, index)) {
                scoreIncrease++;
            }
        }
    });

    return scoreIncrease;
}
