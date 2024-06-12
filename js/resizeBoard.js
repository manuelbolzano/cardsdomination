export function adjustBoardSize() {
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
