<?php

use App\Pizza;
use Illuminate\Database\Seeder;

class PizzaSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Pizza::truncate();

		$pizzas = [
			[
				'name' => 'Margherita',
				'ingredients' => 'mozzarella, tomato & basil',
				'price' => 16.50,
				'image' => 'https://img2.freepng.es/20180513/wkq/kisspng-california-style-pizza-sicilian-pizza-pizza-marghe-5af8211cd60025.2324954415262108448766.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'name' => 'Tropical',
				'ingredients' => 'mozzarella, tomato, ham & pineapple',
				'price' => 17.50,
				'image' => 'https://img2.freepng.es/20180604/plo/kisspng-california-style-pizza-sicilian-pizza-hawaiian-piz-hawaiian-pizza-5b15bd83c479e7.9233370615281514278048.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'name' => 'Capricciosa',
				'ingredients' => 'mozzarella, tomato, ham, mushrooms, olives & anchovies',
				'price' => 17.50,
				'image' => 'https://img2.freepng.es/20180408/wjw/kisspng-pizza-margherita-doner-kebab-ham-pesto-fungi-5aca84b612d353.5082666715232216860771.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'name' => 'Vegetarian',
				'ingredients' => 'mozzarella, tomato, mushrooms, onions, peppers, pineapple, olives & capers',
				'price' => 17.50,
				'image' => 'https://img2.freepng.es/20180630/vc/kisspng-vegetarian-cuisine-domino-s-pizza-garlic-bread-ham-5b37a16d26a037.5282653815303724611582.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'name' => 'Patata',
				'ingredients' => 'mozzarella, onion, potato, rosemary, salt, garlic & parsley',
				'price' => 17.50,
				'image' => 'https://img2.freepng.es/20180528/oit/kisspng-dish-network-recipe-cuisine-mixture-pizza-potato-5b0bb0c40b3415.2231539015274928040459.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'name' => 'Ausie',
				'ingredients' => 'mozzarella, tomato, bacon & egg',
				'price' => 17.50,
				'image' => 'https://img2.freepng.es/20180323/ale/kisspng-pizza-bacon-hamburger-barbecue-sauce-bacon-5ab4b28d1fa678.2294045715217916291296.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'name' => 'Meat Lovers',
				'ingredients' => 'mozzarella, tomato, ham, bacon, sopressa salami & BBQ sauce',
				'price' => 18.00,
				'image' => 'https://img2.freepng.es/20180404/ttw/kisspng-pizza-salami-ham-italian-cuisine-barbecue-pizza-5ac59dd3ce75b1.7707154215229004358457.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'name' => 'Calabrese',
				'ingredients' => 'mozzarella, tomato, sopressa salami & peppers',
				'price' => 18.00,
				'image' => 'https://img2.freepng.es/20180511/vqw/kisspng-pizza-salami-italian-cuisine-ham-prosciutto-5af5f12cd29e28.9362917315260675008627.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
		];
		Pizza::insert($pizzas);
	}
}
