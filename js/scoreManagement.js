export function updateScores(cellId, side, board, userScore, aiScore) {
    let adjacentIndices = getAdjacentIndices(cellId);
    adjacentIndices.forEach(index => {
        if (board[index] && board[index].side !== side) {
            let currentCard = board[cellId].card;
            let adjacentCard = board[index].card;
            if (compareCards(currentCard, adjacentCard, cellId, index)) {
                if (side === "user") {
                    userScore += 1; // Incrementare il punteggio per la vittoria su una carta
                    aiScore -= 1; // Decrementare il punteggio dell'AI per la sconfitta della carta
                } else {
                    aiScore += 1; // Incrementare il punteggio per la vittoria su una carta
                    userScore -= 1; // Decrementare il punteggio dell'utente per la sconfitta della carta
                }
                board[index].side = side;
                document.getElementById("cell-" + index).style.backgroundColor = side === "user" ? "#d1e7dd" : "#f8d7da"; // Cambia il colore della carta conquistata
            }
        }
    });
    document.getElementById("user-score").innerText = userScore;
    document.getElementById("ai-score").innerText = aiScore;
}

export function checkGameEnd(board) {
    return board.every(cell => cell !== null);
}

function getAdjacentIndices(cellId) {
    let indices = [];
    if (cellId % 3 !== 0) indices.push(cellId - 1); // Left
    if (cellId % 3 !== 2) indices.push(cellId + 1); // Right
    if (cellId >= 3) indices.push(cellId - 3); // Top
    if (cellId < 6) indices.push(cellId + 3); // Bottom
    if (cellId % 3 !== 0 && cellId >= 3) indices.push(cellId - 4); // Top-left
    if (cellId % 3 !== 2 && cellId >= 3) indices.push(cellId - 2); // Top-right
    if (cellId % 3 !== 0 && cellId < 6) indices.push(cellId + 2); // Bottom-left
    if (cellId % 3 !== 2 && cellId < 6) indices.push(cellId + 4); // Bottom-right
    return indices;
}

function compareCards(currentCard, adjacentCard, cellId, adjacentCellId) {
    // Confrontare i valori delle carte in base alla posizione
    if (cellId - 1 === adjacentCellId) return currentCard.power_left > adjacentCard.power_right; // Sinistra
    if (cellId + 1 === adjacentCellId) return currentCard.power_right > adjacentCard.power_left; // Destra
    if (cellId - 3 === adjacentCellId) return currentCard.power_top > adjacentCard.power_bottom; // Sopra
    if (cellId + 3 === adjacentCellId) return currentCard.power_bottom > adjacentCard.power_top; // Sotto
    if (cellId - 4 === adjacentCellId) return currentCard.power_top_left > adjacentCard.power_bottom_right; // Top-left
    if (cellId - 2 === adjacentCellId) return currentCard.power_top_right > adjacentCard.power_bottom_left; // Top-right
    if (cellId + 2 === adjacentCellId) return currentCard.power_bottom_left > adjacentCard.power_top_right; // Bottom-left
    if (cellId + 4 === adjacentCellId) return currentCard.power_bottom_right > adjacentCard.power_top_left; // Bottom-right
    return false;
}
