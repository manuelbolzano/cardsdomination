let selectedCardId = null;
let selectedCellId = null;
let board = [null, null, null, null, null, null, null, null, null];
let userScore = 0;
let aiScore = 0;
let aiCards = JSON.parse(document.getElementById('ai-cards-data').textContent);
let userCards = JSON.parse(document.getElementById('user-cards-data').textContent);

function selectCard(cardId) {
    selectedCardId = cardId;
    console.log("Card selected: " + cardId);
}

function selectCell(cellId) {
    selectedCellId = cellId;
    console.log("Cell selected: " + cellId);
    if (selectedCardId !== null && board[cellId] === null) {
        playCard(selectedCardId, cellId, "user");
    }
}

function playCard(cardId, cellId, side) {
    console.log("Playing card " + cardId + " in cell " + cellId);
    let card = side === "user" ? getCardById(cardId, 'user') : getCardById(cardId, 'ai');
    board[cellId] = { card: card, side: side };

    // Rimuovere la carta dal deck
    if (side === "user") {
        userScore++;
        removeCardFromDeck(cardId, 'user');
    } else {
        aiScore++;
        removeCardFromDeck(cardId, 'ai');
    }

    updateBoard();
    updateScores(cellId, side);

    selectedCardId = null;
    selectedCellId = null;

    if (checkGameEnd()) {
        setTimeout(showEndGamePopup, 1000); // Attendere 1 secondo prima di mostrare il popup di fine partita
    } else {
        if (side === "user") {
            setTimeout(aiTurn, 1000); // Attendere 1 secondo prima del turno dell'AI
        }
    }
}

function getCardById(cardId, side) {
    let cards = side === "user" ? userCards : aiCards;
    return cards.find(card => card.id == cardId);
}

function removeCardFromDeck(cardId, side) {
    let cardClass = side === "user" ? `user-card-${cardId}` : `ai-card-${cardId}`;
    let card = document.querySelector(`.${cardClass}`);
    if (card) {
        card.style.display = 'none';
    }

    if (side === "user") {
        userCards = userCards.filter(card => card.id !== cardId);
        document.getElementById('user-cards-data').textContent = JSON.stringify(userCards);
    } else {
        aiCards = aiCards.filter(card => card.id !== cardId);
        document.getElementById('ai-cards-data').textContent = JSON.stringify(aiCards);
    }
}

function updateBoard() {
    for (let i = 0; i < board.length; i++) {
        let cell = document.getElementById("cell-" + i);
        if (board[i] !== null) {
            cell.innerHTML = `
                <div class="card" style="width: 100%; height: 100%; position: relative; background-image: url('${board[i].card.image_url}');">
                    <div class="value top">${board[i].card.power_top}</div>
                    <div class="value top-right">${board[i].card.power_top_right}</div>
                    <div class="value right">${board[i].card.power_right}</div>
                    <div class="value bottom-right">${board[i].card.power_bottom_right}</div>
                    <div class="value bottom">${board[i].card.power_bottom}</div>
                    <div class="value bottom-left">${board[i].card.power_bottom_left}</div>
                    <div class="value left">${board[i].card.power_left}</div>
                    <div class="value top-left">${board[i].card.power_top_left}</div>
                </div>`;
            cell.style.backgroundColor = board[i].side === "user" ? "#d1e7dd" : "#f8d7da"; // Verde per l'utente, rosso per l'AI
        } else {
            cell.innerHTML = "";
            cell.style.backgroundColor = "#e9ecef";
        }
    }
}

function updateScores(cellId, side) {
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

function checkGameEnd() {
    return board.every(cell => cell !== null);
}

function showEndGamePopup() {
    let winner = userScore > aiScore ? "Hai vinto!" : userScore < aiScore ? "Hai perso!" : "Pareggio!";
    
    // Determina i punti da assegnare o sottrarre
    let points = 0;
    let coins = 0;

    if (userScore > aiScore) {
        let difference = userScore - aiScore;
        if (difference > 3) {
            points = 15;
        } else {
            points = 8;
        }
        coins = calculateCoins('win');
    } else if (userScore < aiScore) {
        points = -5;
        coins = calculateCoins('lose');
    }

    // Aggiorna il messaggio del popup con i punti guadagnati o persi
    let pointsMessage = points > 0 ? `Hai guadagnato ${points} punti!` : `Hai perso ${Math.abs(points)} punti.`;
    let coinsMessage = coins > 0 ? `Hai guadagnato ${coins} monete!` : `Hai perso ${Math.abs(coins)} monete.`;
    document.getElementById('endGameMessage').textContent = `${winner} ${pointsMessage} ${coinsMessage}`;

    // Invia la richiesta AJAX per aggiornare il punteggio e le monete
    updatePointsAndCoins(points, userScore, aiScore, coins);

    $('#endGameModal').modal('show');
}

function calculateCoins(result) {
    let coins = 0;
    let levelInfo = document.getElementById('level-info');
    let minCoins = parseInt(levelInfo.getAttribute(`data-${result}-coins-min`));
    let maxCoins = parseInt(levelInfo.getAttribute(`data-${result}-coins-max`));

    coins = Math.floor(Math.random() * (maxCoins - minCoins + 1)) + minCoins;
    return coins;
}

function updatePointsAndCoins(points, userScore, aiScore, coins) {
    console.log('Sending data:', { points: points, user_score: userScore, ai_score: aiScore, coins: coins });
    fetch('update_points.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ points: points, user_score: userScore, ai_score: aiScore, coins: coins })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function aiTurn() {
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

document.addEventListener('DOMContentLoaded', function() {
    adjustBoardSize();
    window.addEventListener('resize', adjustBoardSize);
});

function adjustBoardSize() {
    const gameContainer = document.querySelector('.game-container');
    const board = document.querySelector('.board');
    const cardContainers = document.querySelectorAll('.cards-container');
    
    const totalHeight = window.innerHeight - document.querySelector('.scoreboard').offsetHeight - 40; // 40px for padding/margin
    const totalWidth = gameContainer.clientWidth;
    const cardContainerWidth = cardContainers[0].clientWidth + cardContainers[1].clientWidth;
    const maxBoardWidth = totalWidth - cardContainerWidth - 40; // 40px for margin
    
    const boardSize = Math.min(maxBoardWidth, totalHeight);
    
    board.style.width = `${boardSize}px`;
    board.style.height = `${boardSize}px`; // Square board
}

window.selectCard = selectCard;
window.selectCell = selectCell;