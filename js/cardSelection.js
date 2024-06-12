export function selectCard(cardId) {
    window.selectedCardId = cardId;
    console.log("Card selected: " + cardId);
}

export function selectCell(cellId) {
    window.selectedCellId = cellId;
    console.log("Cell selected: " + cellId);
    if (window.selectedCardId !== null && window.board[cellId] === null) {
        window.playCard(window.selectedCardId, cellId, "user");
    }
}
