<?php

require 'config.php';

try {
	$db = new PDO($config['db']['connection_string'], $config['db']['user'], $config['db']['pass']);	
	$db->beginTransaction();

	$createTable = 'create table if not exists `products`(
		`id` int primary key auto_increment,
		`name` varchar(255) not null,
		`description` varchar(1000) not null,
		`price` float not null,
		`img` varchar(255) not null
	)';

	$items = [
		[
			'name' => 'Apple',
			'description' => 'Donec placerat eros ante, quis auctor nulla tempor at. Aenean feugiat, quam eget lobortis facilisis, leo velit feugiat quam, rutrum tempus nunc purus a nunc. Praesent vitae feugiat velit. Aliquam vel libero tristique justo vehicula elementum. Fusce bibendum faucibus velit, ac pulvinar nisi lobortis sagittis. Mauris venenatis, dolor at blandit scelerisque, ligula sapien fringilla nisi, nec rhoncus magna enim varius erat. Duis hendrerit, quam sed scelerisque convallis, arcu ligula fringilla leo, nec auctor eros nunc eu nisi. Etiam at ullamcorper neque, quis elementum lacus. Donec suscipit dolor vel augue euismod tincidunt. Morbi in malesuada sem. Fusce ut odio at mauris tristique imperdiet. Vivamus vel egestas velit, nec blandit arcu.',
			'price' => 0.3,
			'img' => 'apple.png'
		],
		[
			'name' => 'Beer',
			'description' => 'Aenean sodales, risus eget volutpat facilisis, tellus diam laoreet nibh, sit amet consectetur odio nibh at purus. Aliquam erat volutpat. Aliquam erat volutpat. Aliquam erat volutpat. Suspendisse potenti. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam id porttitor leo, id tincidunt ipsum. Suspendisse vitae neque nec mauris iaculis tincidunt ac id sem. Etiam auctor mi in dignissim cursus. Fusce ac facilisis dui, non laoreet orci. Nulla facilisi.',
			'price' => 2.0,
			'img' => 'beer.png'
		],
		[
			'name' => 'Water',
			'description' => 'Ut vel enim ac risus rhoncus rutrum ut nec diam. Nunc sit amet risus dui. Curabitur sed elit tristique, vestibulum sapien ut, sollicitudin tortor. Ut aliquet turpis cursus, euismod purus sit amet, placerat eros. Praesent eu ante faucibus, mollis nibh in, gravida metus. Mauris massa nisi, cursus id convallis at, suscipit bibendum est. Aenean vel nisl luctus, accumsan dui quis, pharetra nisi. Sed vulputate sapien leo, sit amet eleifend enim elementum vitae. Aenean condimentum porttitor interdum.',
			'price' => 1.0,
			'img' => 'water.png'
		],
		[
			'name' => 'Cheese',
			'description' => 'Donec accumsan lacus vitae risus condimentum vulputate. In pretium mollis ornare. Duis euismod est at bibendum pharetra. Cras pellentesque mauris nec dolor mollis, consectetur molestie ligula vulputate. Curabitur in augue rutrum, lacinia lorem at, aliquam tellus. Nunc tempor ut nunc ut aliquam. Nunc vel sem vitae nunc placerat commodo non ut mi. Vestibulum id posuere ipsum. Etiam tempus ex eu leo vulputate sollicitudin. Duis vel rhoncus nisi. Nullam sagittis ac velit a consequat.',
			'price' => 3.74,
			'img' => 'cheese.png'
		]
	];

	$db->exec($createTable);

	foreach ($items as $item) {
		$dbst = $db->prepare('insert into `products`(`name`, `description`, `price`, `img`) values (:name, :description, :price, :img)');
		$dbst->bindParam(':name', $item['name']);
		$dbst->bindParam(':description', $item['description']);
		$dbst->bindParam(':price', $item['price']);
		$dbst->bindParam(':img', $item['img']);
		$dbst->execute();
	}

	$db->commit();
	echo 'Done.';
} catch (PDOException $e) {
	echo "Couldn\'t generate the data: ". $e->getMessage();
}