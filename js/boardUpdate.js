export function updateBoard(board) {
    for (let i = 0; i < board.length; i++) {
        let cell = document.getElementById("cell-" + i);
        if (board[i] !== null) {
            cell.innerHTML = `
                <div class="card" style="width: 100%; height: 100%; position: relative; background-image: url('${board[i].card.image_url}'); background-size: contain;">
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

export function removeCardFromDeck(cardId, side, cards) {
    let cardClass = side === "user" ? `user-card-${cardId}` : `ai-card-${cardId}`;
    let card = document.querySelector(`.${cardClass}`);
    if (card) {
        card.style.display = 'none';
    }

    if (side === "user") {
        cards = cards.filter(card => card.id !== cardId);
        document.getElementById('user-cards-data').textContent = JSON.stringify(cards);
    } else {
        cards = cards.filter(card => card.id !== cardId);
        document.getElementById('ai-cards-data').textContent = JSON.stringify(cards);
    }
}
