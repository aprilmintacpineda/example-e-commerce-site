<?php
	session_start();

	require '../core/init.php';

	if (!isset($_SESSION['cart'])) {
		$_SESSION['cart'] = [];
	}

	if (!isset($_SESSION['user_balance'])) {
		$_SESSION['user_balance'] = 100;
	}

	if (!isset($_SESSION['purchases'])) {
		$_SESSION['purchases'] = [];
	}

	function in_cart($target) {
		foreach ($_SESSION['cart'] as $item) {
			if ($item->id == $target['id']) {
				return $item;
			}
		}

		return false;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Interview Answer</title>
	<meta charset="utf-8">
	<meta name="description" content="You don't need to check this out, really I did this for my interview. No copy right infringement intended, this is not for commercial use.">
	<meta name="author" content="April Mintac Pineda">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans:100|Roboto:300" rel="stylesheet"> 
	<link rel="stylesheet" type="text/css" href="css/globals.css">
	<link rel="stylesheet" type="text/css" href="css/cart.css">
	<link rel="stylesheet" type="text/css" href="css/sidebar.css">
	<link rel="stylesheet" type="text/css" href="css/main-contents.css">
	<link rel="stylesheet" type="text/css" href="css/banner.css">
	<link rel="stylesheet" type="text/css" href="css/modal.css">
	<link rel="stylesheet" type="text/css" href="css/purchase-history.css">
</head>
<body>
	<?php include '../partials/sidenav.html'; ?>
	<div class="main-contents">
		<div class="banner">
			<div class="cover"></div>
			<h1 class="p-center">Lorem ipsum dolor sit amet</h1>
		</div>
		<div class="item-list clear-float">
			<?php
				foreach ($items as $item) {
					$inCart = in_cart($item);

					echo '
						<div class="items">
							<img src="images/'. $item['img'] .'"/>
							<div class="flexible-border-bottom"><strong>'. $item['name'] .'</strong></div>
							<div>'. substr($item['description'], 0, 65) . (strlen($item['description']) > 65? '...' : '') .'</div>
							<div id="item_id_'. $item['id'] .'" class="buttons">
								'. ($inCart
										? '<span class="remove-from-cart" data-item=\''. json_encode($item) .'\'>
												<span class="cross v-center">x</span><span class="text">Remove from cart</span>
											</span>'
										: '<span class="add-to-cart" data-item=\''. json_encode($item) .'\'>
												<span class="text">Add to cart <small class="v-center">$'. $item['price'] .'</small></span>
												<span class="bg">Buy Me!</span>
											</span>') .'
							</div>
						</div>
					';
				}
			?>
		</div>
		<footer>
			All rights reserved
		</footer>
	</div>
	<div class="cart-wrapper">
		<img class="toggle-cart" src="images/cart.png" alt="Cart" title="Virtual Cart">
		<div id="cart" class="cart hidden">
			<section id="cart-item-list" class="item-list"></section>
			<section class="cart-information"></section>
		</div>
	</div>
	<div class="purchase-history-wrapper">
		<img class="toggle-purchase-history" src="images/history.png" alt="Purchase History" title="Purchase history">
		<div id="purchase-history" class="purchase-history hidden">
			<section id="purchase-history-list" class="item-list"></section>
			<section class="balance-information"></section>
		</div>
	</div>
	<script>
		(function() {
			/**
			 * libraries
			 */
			var $cash = function(amount) {
				var _this = this;
				this.$amount = amount;
				this.deduct = function(amount) {
					_this.$amount = _this.$amount - amount;
					return _this;
				}
			}

			var $cart = function(initial_state) {
				var _this = this;
				var cart = document.querySelector('#cart');
				var itemList = document.querySelector('#cart-item-list');
				
				this.$state = initial_state;
				this.toggle = function() {
					_this.$state.shown = !_this.$state.shown;
					if (_this.$state.shown) {
						cart.setAttribute('class', 'cart');
					} else {
						cart.setAttribute('class', 'cart hidden');
					}
					return _this;
				};
				this.changeTransportType = function(transportType) {
					_this.$state.transportType = transportType;
					return _this;
				}
				this.getTransportType = function() {
					return _this.$state.transportType;
				}
				this.hide = function() {
					_this.$state.shown = false;
					cart.setAttribute('class', 'cart hidden');
				}
				this.isEmpty = function() {
					return _this.$state.data.length == 0;
				}
				this.updateCart = function(items) {
					_this.$state.data = items;
					_this.render();
					return _this;
				};
				this.getItems = function() {
					return _this.$state.data;
				}
				this.amountPayable = function(disregard_id) {
					return parseFloat(_this.$state.data.reduce(function(sum, current) {
						return current.id == disregard_id
							? sum
							: sum + (current.price * current.quantity);
					}, 0)) + (_this.$state.transportType == 2? 5 : 0);
				}
				this.clean = function() {
					_this.$state = {
						shown: false,
						data: []
					};
					_this.render();
					return _this;
				}
				this.render = function() {
					itemList.innerHTML = '';
					if (!_this.$state.data.length) {
						itemList.innerHTML = '<p class="p-center">Your cart is empty.</p>';
					} else {
						for (var a = 0; a < _this.$state.data.length; a++) {
							itemList.innerHTML += `
								<div class="item flexible-border-bottom clear-float">
									<img src="images/${_this.$state.data[a].img}">
									<div class="item-details">
										<p><strong>${_this.$state.data[a].name}</strong></p>
										<p>Price: $${_this.$state.data[a].price}</p>
										<p>Quantity: ${_this.$state.data[a].quantity} pcs</p>
										<p>Total: $${(_this.$state.data[a].quantity * _this.$state.data[a].price).toFixed(2)}</p>
										<p><a class="item-change-quantity" data-item='${JSON.stringify(_this.$state.data[a])}'>Change quantity</a> | <a class="remove-from-cart" data-item='${JSON.stringify(_this.$state.data[a])}'>Remove</a></p>
									</div>
								</div>
							`;
						}
					}
					return _this;
				}
				this.render();
				return this;
			}

			var $modal = function(content) {
				var _this = this;
				var modal = document.createElement('div');
				modal.setAttribute('class', 'modal-wrapper');
				modal.innerHTML = `<div class="modal-overlay h-center">${content}</div>`;
				document.body.style.overflow = 'hidden';
				document.body.appendChild(modal);
				this.dismiss = function() {
					document.body.removeChild(document.querySelector('.modal-wrapper'));
					document.body.style.overflow = 'auto';
				}
				this.changeContent = function(newContent) {
					modal.innerHTML = `<div class="modal-overlay h-center">${newContent}</div>`;
				}
				return this;
			}

			var $purchases = function(initial_state) {
				var _this = this;
				var itemList = document.querySelector('#purchase-history-list');
				var purchaseHistory = document.querySelector('#purchase-history');
				this.$state = initial_state;
				this.updatePurchases = function(newPurchases) {
					_this.$state.data = newPurchases;
					_this.render();
					return _this;
				}
				this.hide = function() {
					_this.$state.shown = false;
					purchaseHistory.setAttribute('class', 'purchase-history hidden');
				}
				this.toggle = function() {
					_this.$state.shown = !_this.$state.shown;
					if (_this.$state.shown) {
						purchaseHistory.setAttribute('class', 'purchase-history');
					} else {
						purchaseHistory.setAttribute('class', 'purchase-history hidden');
					}
				}
				this.render = function() {
					itemList.innerHTML = '';
					if (!_this.$state.data.length) {
						itemList.innerHTML = `<p class="p-center">You haven't purchased anything.</p>`;
					} else {
						for (var a = 0; a < _this.$state.data.length; a++) {
							var items = '';

							_this.$state.data[a].items.forEach(function(item, i) {
								var html = '';
								html += `${item.name} ... <strong>${parseFloat(item.price).toFixed(2)}</strong> ... <strong>${item.quantity} pc(s)</strong> ... <strong>${(item.quantity * item.price).toFixed(2)}</strong>`;
								items += `<p>${html}</p>`;
							});

							itemList.innerHTML += `
								<section class="purchase-item flexible-border-bottom">
									<p>ID: ${_this.$state.data[a].id}</p>
									<p>------</p>
									${items}
									<p>Transport type ... <strong>${
										_this.$state.data[a].transportType == 1
										? `Pick up`
										: `UPS`
									}</strong> ... <strong>$${parseFloat(_this.$state.data[a].transportFee).toFixed(2)}</strong></p>
									<p>------</p>
									<p>Balance before: <strong>$${parseFloat(_this.$state.data[a].balanceBefore).toFixed(2)}</strong></p>
									<p>Total amount paid: <strong>$${parseFloat(_this.$state.data[a].sum).toFixed(2)}</strong></p>
									<p>Balance after: <strong>$${parseFloat(_this.$state.data[a].balanceAfter).toFixed(2)}</strong></p>
								</section>
							`;
						}
					}
				}
				this.render();
				return this;
			}

			// set up
			var cart = new $cart({
				shown: false,
				data: <?= json_encode($_SESSION['cart']) ?>,
				transportType: null
			});
			var cash = new $cash(<?= $_SESSION['user_balance'] ?>);
			var purchases = new $purchases({
				shown: false,
				data: <?= json_encode($_SESSION['purchases']) ?>
			});

			triggers();

			document.querySelector('.toggle-cart').onclick = function() {
				cart.toggle();
				purchases.hide();
			}

			document.querySelector('.toggle-purchase-history').onclick = function() {
				purchases.toggle();
				cart.hide();
			}

			/**
			 * functions
			 */
			
			function triggers() {
				updateCartInfo();
				triggerAddToCart();
				triggerRemoveFromCart();
				triggerChangeQuantity();
				triggerCheckout();
			}

			function updateCartInfo() {
				var cartInfo = document.querySelector('.cart-information');
				var transportType = cart.getTransportType();
				cartInfo.innerHTML = `
					<p>
						Transport type: <a id="change-transport-type">${
							transportType == 1
							? 'Pick up'
							: transportType == 2
							? 'UPS'
							: 'Choose one'
						}</a>
					</p>
					<p>Amount to pay: $${cart.amountPayable().toFixed(2)}</p>
					<p>Current balance: $${cash.$amount.toFixed(2)}</p>
					<p>After Balance: $${(cash.$amount - cart.amountPayable()).toFixed(2)}</p>
					${cart.isEmpty()? `` : `<span class="checkout v-center">Checkout<span>&raquo;</span></span>`}
				`;

				document.querySelector('#change-transport-type').onclick = function() {
					var modal = new $modal(`
						<section>
							<label>
								<p>Choose your preferred transport type:</p>
								<select id="transport-type" class="select-default">
									<option>-- Choose one --</option>
									<option value="1"${transportType == 1? ' SELECTED' : ''}>Pick up</option>
									<option value="2"${transportType == 2? ' SELECTED' : ''}>UPS</option>
								</select>
							</label>
						</section>
						<section id="transport-messages"></section>
						<section>
							<input id="transport-type-confirm" class="input-btn btn-primary" type="button" value="Okay"> <input id="transport-type-cancel" class="input-btn btn-danger" type="button" value="Cancel">
						</section>
					`);

					document.querySelector('#transport-type').onchange = function(ev) {
						var transMess = document.querySelector('#transport-messages');
						transMess.innerHTML = '';

						if (ev.target.value == 2) {
							transMess.innerHTML = `<p>Additional of <strong>$5.00</strong> as transport fee.</p>`;
						}
					}

					document.querySelector('#transport-type-confirm').onclick = function(ev) {
						var val = document.querySelector('#transport-type').value;

						if (val != 1 && val != 2) {
							cart.changeTransportType(null);
						} else {
							cart.changeTransportType(val);
						}

						modal.dismiss();
						triggers();
					}

					document.querySelector('#transport-type-cancel').onclick = modal.dismiss;
				}
			}

			function triggerCheckout() {
				var checkout = document.querySelector('.checkout');

				if (checkout) {
					checkout.onclick = function() {
						var modal = new $modal(
							`<p>Please wait...</p>`
						);

						if (cart.getTransportType() != 1 && cart.getTransportType() != 2) {
							modal.changeContent(`
								<section>
									<p>Please choose your preferred transport type.</p>
								</section>
								<section>
									<input id="modal-dismiss" class="input-btn btn-primary" type="button" value="Okay">
								</section>
							`);

							document.querySelector('#modal-dismiss').onclick = modal.dismiss;
						} else if (cash.$amount - cart.amountPayable() > 0) {
							var data = new FormData();
							data.append('cart', JSON.stringify(cart.getItems()));
							data.append('sum', JSON.stringify(cart.amountPayable()));
							data.append('transportType', JSON.stringify(cart.getTransportType()));
							fetch('checkout.php', {
								credentials: 'include',
								method: 'POST',
								body: data
							})
							.then(function(r) {
								return r.json();
							})
							.then(function(data) {
								purchases.updatePurchases(data);
								cart.clean();
								cart.hide();
								purchases.hide();

								document.querySelectorAll('.buttons').forEach(function(el) {
									var targetItem = el.querySelector('.remove-from-cart');

									if (targetItem) {
										targetItem = JSON.parse(targetItem.getAttribute('data-item'));

										el.innerHTML = `
											<span class="add-to-cart" data-item='${JSON.stringify(targetItem)}'>
												<span class="text">Add to cart <small class="v-center">$${targetItem.price}</small></span>
												<span class="bg">Buy Me!</span>
											</span>
										`;
									}
								});

								triggers();

								modal.changeContent(`
									<section>
										<p>Your order has been placed. You can check your purchase history to see it.</p>
									</section>
									<section>
										<input id="modal-dismiss" class="input-btn btn-primary" type="button" value="Okay">
									</section>
								`);

								document.querySelector('#modal-dismiss').onclick = modal.dismiss;
							})
							.catch(function(e) {
								console.error(e);
							});
						} else {
							modal.changeContent(`
								<p>You don't have sufficient balance to checkout. Please change the quantity of some of the products in your cart.</p>
							`);
						}
					}
				}
			}

			function triggerChangeQuantity() {
				document.querySelectorAll('.item-change-quantity').forEach(function(el) {
					el.onclick = function() {
						var targetItem = JSON.parse(el.getAttribute('data-item'));
						var modal = new $modal(`
							<section>
								<label>
									<p>How many are you planning to purchase?</p>
									<input id="purchase-quantity" class="input-text txt-prime" type="text" placeholder="e.g., 10" value="${targetItem.quantity}">
								</label>
							</section>
							<section id="purchase-messages"></section>
							<section>
								<input id="change-quantity-confirm" class="input-btn btn-primary" type="button" value="Change quantity"> <input id="change-quantity-cancel" class="input-btn btn-danger" type="button" value="Cancel">
							</section>
						`);

						var errorMessage = '';
						var purchaseQuantity = document.querySelector('#purchase-quantity');
						var purchaseMessages = document.querySelector('#purchase-messages');

						document.querySelector('#change-quantity-cancel').onclick = modal.dismiss;
						purchaseQuantity.onkeypress = purchaseQuantity.onkeydown = purchaseQuantity.onkeyup = function() {
							purchaseMessages.innerHTML = '';
							errorMessage = '';
							var value = purchaseQuantity.value;

							if (value.length) {
								if (isNaN(value) || value == 0) {
									errorMessage = 'Please enter a valid number.';
									purchaseMessages.innerHTML = `<p class="label-danger">${errorMessage}</p>`;
								} else if (cash.$amount < targetItem.price * value || cart.amountPayable() + targetItem.price * value > cash.$amount) {
									errorMessage = 'You don\'t have sufficient balance to buy that much.';
									purchaseMessages.innerHTML = `<p class="label-danger">${errorMessage}</p>`;
									calc();
								} else {
									calc();
								}
							} else {
								errorMessage = 'Please enter a valid number.';
								purchaseMessages.innerHTML = `<p class="label-danger">${errorMessage}</p>`;
							}

							function calc() {
								purchaseMessages.innerHTML += `
									<p>Amount to pay: <strong>$${(targetItem.price * value).toFixed(2)}</strong></p>
									<p>Current amount in cart: <strong>$${cart.amountPayable().toFixed(2)}</strong></p>
									<p>After amount in cart: <strong>$${(cart.amountPayable(targetItem.id) + (targetItem.price * value)).toFixed(2)}</strong></p>
								`;
							}
						}
						document.querySelector('#change-quantity-confirm').onclick = function() {
							if (!errorMessage) {
								modal.changeContent(`<p>Please wait...</p>`);

								var data = new FormData();
								var items = cart.getItems().map(function(item) {
									if (item.id == targetItem.id) {
										return Object.assign(item, {
											quantity: purchaseQuantity.value
										});
									}

									return item;
								});
								data.append('cart', JSON.stringify(items));

								fetch('updateCart.php', {
									credentials: 'include',
									method: 'POST',
									body: data
								})
								.then(function(r) {
									return r.json();
								})
								.then(function(data) {
									cart.updateCart(data);
									el.parentNode.innerHTML = `
										<span class="remove-from-cart" data-item='${JSON.stringify(targetItem)}'>
											<span class="cross v-center">x</span><span class="text">Remove from cart</span>
										</span>
									`;
									triggers();
									modal.dismiss();
								})
								.catch(function(e) {
									console.error(e);
								});
							} else {
								purchaseMessages.innerHTML = `<p class="label-danger">${errorMessage}</p>`;
							}
						}
					}
				});
			}

			function triggerAddToCart() {
				document.querySelectorAll('.add-to-cart').forEach(function(el) {
					el.onclick = function() {
						var targetItem = JSON.parse(el.getAttribute('data-item'));

						var modal = new $modal(`
							<section>
								<label>
									<p>How many are you planning to purchase?</p>
									<input id="purchase-quantity" class="input-text txt-prime" type="text" placeholder="e.g., 10">
								</label>
							</section>
							<section id="purchase-messages"></section>
							<section>
								<input id="add-to-cart-confirm" class="input-btn btn-primary" type="button" value="Add to cart"> <input id="add-to-cart-cancel" class="input-btn btn-danger" type="button" value="Cancel">
							</section>
						`);

						var errorMessage = 'Please enter a valid number.';
						var purchaseQuantity = document.querySelector('#purchase-quantity');
						var purchaseMessages = document.querySelector('#purchase-messages');

						document.querySelector('#add-to-cart-cancel').onclick = modal.dismiss;
						purchaseQuantity.onkeypress = purchaseQuantity.onkeydown = purchaseQuantity.onkeyup = function() {
							purchaseMessages.innerHTML = '';
							errorMessage = '';
							var value = purchaseQuantity.value;

							if (value.length) {
								if (isNaN(value) || value == 0) {
									errorMessage = 'Please enter a valid number.';
									purchaseMessages.innerHTML = `<p class="label-danger">${errorMessage}</p>`;
								} else if (cash.$amount < targetItem.price * value || cart.amountPayable() + targetItem.price * value > cash.$amount) {
									errorMessage = 'You don\'t have sufficient balance to buy that much.';
									purchaseMessages.innerHTML = `<p class="label-danger">${errorMessage}</p>`;
									calc();
								} else {
									calc();
								}
							} else {
								errorMessage = 'Please enter a valid number.';
								purchaseMessages.innerHTML = `<p class="label-danger">${errorMessage}</p>`;
							}

							function calc() {
								purchaseMessages.innerHTML += `
									<p>Amount to pay: <strong>$${(targetItem.price * value).toFixed(2)}</strong></p>
									<p>Current amount in cart: <strong>$${cart.amountPayable().toFixed(2)}</strong></p>
									<p>After amount in cart: <strong>$${(cart.amountPayable() + (targetItem.price * value)).toFixed(2)}</strong></p>
								`;
							}
						}

						document.querySelector('#add-to-cart-confirm').onclick = function() {
							if (!errorMessage) {
								modal.changeContent(`<p>Please wait...</p>`);

								var data = new FormData();
								var items = cart.getItems();
								items.push(Object.assign({
									quantity: purchaseQuantity.value
								}, targetItem));
								data.append('cart', JSON.stringify(items));

								fetch('updateCart.php', {
									credentials: 'include',
									method: 'POST',
									body: data
								})
								.then(function(r) {
									return r.json();
								})
								.then(function(data) {
									cart.updateCart(data);
									el.parentNode.innerHTML = `
										<span class="remove-from-cart" data-item='${JSON.stringify(targetItem)}'>
											<span class="cross v-center">x</span><span class="text">Remove from cart</span>
										</span>
									`;
									triggers();
									modal.dismiss();
								})
								.catch(function(e) {
									console.error(e);
								});
							} else {
								purchaseMessages.innerHTML = `<p class="label-danger">${errorMessage}</p>`;
							}
						}
					}
				});
			}

			function triggerRemoveFromCart() {
				document.querySelectorAll('.remove-from-cart').forEach(function(el) {
					el.onclick = function () {
						var modal = new $modal('<p>Please wait...</p>');
						var data = new FormData();
						var targetItem = JSON.parse(el.getAttribute('data-item'));
						var items = cart.getItems().filter(function(item) {
							return item.id != targetItem.id
						});
						data.append('cart', JSON.stringify(items));

						fetch('updateCart.php', {
							credentials: 'include',
							method: 'POST',
							body: data
						})
						.then(function(r) {
							return r.json();
						})
						.then(function(data) {
							cart.updateCart(data);
							var item_container = document.querySelector('#item_id_' + targetItem.id);
							if (item_container) {
								item_container.innerHTML = `
									<span class="add-to-cart" data-item='${JSON.stringify(targetItem)}'>
										<span class="text">Add to cart <small class="v-center">$${targetItem.price}</small></span>
										<span class="bg">Buy Me!</span>
									</span>
								`;
							} else {
								el.parentNode.innerHTML = `
									<span class="add-to-cart" data-item='${JSON.stringify(targetItem)}'>
										<span class="text">Add to cart <small class="v-center">$${targetItem.price}</small></span>
										<span class="bg">Buy Me!</span>
									</span>
								`;
							}
							triggers();
							modal.dismiss();
						})
						.catch(function(e) {
							console.error(e);
						});
					}
				});
			}
		})();
	</script>
</body>
</html>