<?php
    include_once('model/rating.php');
    $rating = rating_all();
?>

<h1>Ставки</h1>

<table cellspacing="2" border="1" cellpadding="5" width="600">
    <thead>
        <tr>
            <td>Користувач</td>
            <td>Тендер</td>
            <td>Ставка</td>
            <td>Дата ставки</td>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rating as $rate): ?>
        <tr>
            <td><?php echo $rate['display_name']; ?></td>
            <td><?php echo $rate['post_title']; ?></td>
            <td><?php echo $rate['price']; ?> грн.</td>
            <td><?php echo $rate['rate_time']; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>