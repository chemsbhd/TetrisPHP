<?php

session_start();

$rows = 20; // nombre de lignes de la grille
$cols = 10; // nombre de colonnes de la grille

// définition des formes des tetrominos
$tetrominos = [
    'I' => [
        [1, 1, 1, 1]
    ],
    'O' => [
        [1, 1],
        [1, 1]
    ],
    'T' => [
        [0, 1, 0],
        [1, 1, 1]
    ],
    'J' => [
        [1, 0, 0],
        [1, 1, 1]
    ],
    'L' => [
        [0, 0, 1],
        [1, 1, 1]
    ],
    'S' => [
        [0, 1, 1],
        [1, 1, 0]
    ],
    'Z' => [
        [1, 1, 0],
        [0, 1, 1]
    ],
];

// couleurs associées à chaque tetromino
$colors = [
    'I' => '#1abc9c',  // turquoise
    'O' => '#f1c40f',  // jaune
    'T' => '#9b59b6',  // violet
    'J' => '#2980b9',  // bleu
    'L' => '#e67e22',  // orange
    'S' => '#27ae60',  // vert
    'Z' => '#e74c3c',  // rouge
];

// si le bouton "recommencer" est cliqué, on réinitialise la partie
if (isset($_POST['action']) && $_POST['action'] === 'clear') {
    unset($_SESSION['grid'], $_SESSION['block'], $_SESSION['score'], $_SESSION['gameover']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// si le jeu est terminé, on bloque tout sauf le bouton "recommencer"
if (isset($_SESSION['gameover']) && $_SESSION['gameover'] === true) {
    $grid = $_SESSION['grid'] ?? array_fill(0, $rows, array_fill(0, $cols, 0));
    $score = $_SESSION['score'] ?? 0;
} else {
    // initialisation de la grille si elle n'existe pas encore
    if (!isset($_SESSION['grid'])) {
        $grid = array_fill(0, $rows, array_fill(0, $cols, 0));
        $_SESSION['grid'] = $grid;
    } else {
        $grid = $_SESSION['grid'];
    }

    // initialisation du score si nécessaire
    if (!isset($_SESSION['score'])) {
        $_SESSION['score'] = 0;
    }
    $score = &$_SESSION['score'];

    // fonction pour créer un nouveau bloc aléatoire
    function newBlock($tetrominos) {
        $keys = array_keys($tetrominos);
        $randKey = $keys[array_rand($keys)];
        return [
            'shape' => $tetrominos[$randKey],
            'row' => 0,
            'col' => 3,
            'type' => $randKey,
        ];
    }

    // initialisation du bloc si nécessaire
    if (!isset($_SESSION['block'])) {
        $_SESSION['block'] = newBlock($tetrominos);
    }
    $block = &$_SESSION['block'];

    // vérifie si un bloc peut se déplacer
    function canMoveBlock($shape, $row, $col, $grid, $rows, $cols) {
        $height = count($shape);
        $width = count($shape[0]);
        for ($r = 0; $r < $height; $r++) {
            for ($c = 0; $c < $width; $c++) {
                if ($shape[$r][$c] === 1) {
                    $newRow = $row + $r;
                    $newCol = $col + $c;
                    if ($newRow < 0 || $newRow >= $rows || $newCol < 0 || $newCol >= $cols) {
                        return false;
                    }
                    if ($grid[$newRow][$newCol] === 1) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    // fait tourner un bloc
    function rotateShape($shape) {
        $height = count($shape);
        $width = count($shape[0]);
        $rotated = [];
        for ($c = 0; $c < $width; $c++) {
            $newRow = [];
            for ($r = $height - 1; $r >= 0; $r--) {
                $newRow[] = $shape[$r][$c];
            }
            $rotated[] = $newRow;
        }
        return $rotated;
    }

    // supprime les lignes pleines et compte combien ont été supprimées
    function clearFullLines(&$grid, $rows, $cols) {
        $linesCleared = 0;
        for ($r = $rows - 1; $r >= 0; $r--) {
            if (!in_array(0, $grid[$r])) {
                $linesCleared++;
                array_splice($grid, $r, 1);
                array_unshift($grid, array_fill(0, $cols, 0));
                $r++;
            }
        }
        return $linesCleared;
    }

    // gestion des mouvements du bloc
    $move = $_POST['move'] ?? null;

    if ($move === 'left' && canMoveBlock($block['shape'], $block['row'], $block['col'] - 1, $grid, $rows, $cols)) {
        $block['col']--;
    } elseif ($move === 'right' && canMoveBlock($block['shape'], $block['row'], $block['col'] + 1, $grid, $rows, $cols)) {
        $block['col']++;
    } elseif ($move === 'rotate') {
        $rotatedShape = rotateShape($block['shape']);
        if (canMoveBlock($rotatedShape, $block['row'], $block['col'], $grid, $rows, $cols)) {
            $block['shape'] = $rotatedShape;
        }
    }

    // fait descendre le bloc ou le fixe si impossible
    if (canMoveBlock($block['shape'], $block['row'] + 1, $block['col'], $grid, $rows, $cols)) {
        $block['row']++;
    } else {
        $height = count($block['shape']);
        $width = count($block['shape'][0]);
        $gameOver = false;
        for ($r = 0; $r < $height; $r++) {
            for ($c = 0; $c < $width; $c++) {
                if ($block['shape'][$r][$c] === 1) {
                    $gridRow = $block['row'] + $r;
                    $gridCol = $block['col'] + $c;
                    $grid[$gridRow][$gridCol] = 1;
                    if ($gridRow === 0) {
                        $gameOver = true;
                    }
                }
            }
        }

        // supprime les lignes pleines et met à jour le score
        $lines = clearFullLines($grid, $rows, $cols);
        if ($lines > 0) {
            $score += $lines;
        }

        // vérifie si le jeu est terminé
        if ($gameOver) {
            $_SESSION['gameover'] = true;
        } else {
            $block = newBlock($tetrominos);
        }
    }

    // sauvegarde l'état actuel
    $_SESSION['grid'] = $grid;
    $_SESSION['block'] = $block;
    $_SESSION['score'] = $score;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Tetris PHP</title>
    <?php if (!isset($_SESSION['gameover']) || !$_SESSION['gameover']): ?>
        <meta http-equiv="refresh" content="1.5" />
    <?php endif; ?>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Tetris PHP</h1>

    <?php if (isset($_SESSION['gameover']) && $_SESSION['gameover']): ?>
        <div id="gameover">GAME OVER</div>
        <div id="score">Score : <?= $score ?></div>
        <form method="post">
            <button type="submit" name="action" value="clear" class="clear">Recommencer</button>
        </form>
    <?php else: ?>
        <div id="score">Score : <?= $score ?></div>
        <table>
            <?php
            $displayGrid = $grid;
            $height = count($block['shape']);
            $width = count($block['shape'][0]);
            for ($r = 0; $r < $height; $r++) {
                for ($c = 0; $c < $width; $c++) {
                    if ($block['shape'][$r][$c] === 1) {
                        $row = $block['row'] + $r;
                        $col = $block['col'] + $c;
                        if ($row >= 0 && $row < $rows && $col >= 0 && $col < $cols) {
                            $displayGrid[$row][$col] = 2;
                        }
                    }
                }
            }
            foreach ($displayGrid as $r => $row): ?>
                <tr>
                    <?php foreach ($row as $c => $cell):
                        $class = '';
                        $style = '';
                        if ($cell === 0) {
                            $class = 'empty';
                        } elseif ($cell === 1) {
                            $class = 'fixed';
                        } elseif ($cell === 2) {
                            $class = 'current';
                            $style = 'background-color: ' . $colors[$block['type']] . ';';
                        }
                        ?>
                        <td class="<?= $class ?>" style="<?= $style ?>"></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <form method="post">
            <button type="submit" name="move" value="left">← Gauche</button>
            <button type="submit" name="move" value="right">Droite →</button>
            <button type="submit" name="move" value="down">↓ Descendre</button>
            <button type="submit" name="move" value="rotate">⟳ Rotation</button>
            <button type="submit" name="action" value="clear" class="clear">Recommencer</button>
        </form>
    <?php endif; ?>
</body>
</html>
