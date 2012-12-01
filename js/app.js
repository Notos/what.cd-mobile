var App = Ember.Application.create();

User = Ember.Object.extend({
	username:null,
});

News = Ember.Object.extend({
	announcements:null,
	blogs:null
});

Info = Ember.Object.extend({
	messages:null,
	notifications:null,
	subscriptions:null
});

App.ApplicationController = Ember.Controller.extend({
	loggedIn:false,
	authkey:null,
	user:null,
	hasMessages:null,
	inbox:null
});

App.ApplicationView = Ember.View.extend({
	templateName:'application',
});

App.NavigationController = Ember.Controller.extend({
	selected: null
});
App.NavbarView = Ember.View.extend({
	templateName: 'navbar',
	NavItemView: Ember.View.extend({
		tagName: 'li',
		classNameBindings: 'isActive:active'.w(),
		isActive: function() {
			return this.get('item') === App.NavigationController.selected;
		}.property('item').cacheable()
	})
});

App.IndexView = Ember.View.extend({
	templateName:'index',
});
App.LoginFormView = Ember.View.extend({
	templateName:'login-form'
});

App.InboxView = Ember.View.extend({
	templateName: 'inbox',
});
App.AlertsView = Ember.View.extend({
	templateName:'alerts',
});
App.NewsView= Ember.View.extend({
	templateName:'news',
});

App.TorrentsView = Ember.View.extend({
	templateName: 'torrents',
});

App.ForumsView = Ember.View.extend({
	templateName: 'forums',
});

App.SubscriptionsView = Ember.View.extend({
	templateName: 'subscriptions',
});

App.Router = Ember.Router.extend({
	root:Ember.Route.extend({
		index:Ember.Route.extend({
			route:'/',
			connectOutlets:function (router) {
				testLogin(function (isAuthed, data) {
					App.ApplicationController.loggedIn = isAuthed;
					if (isAuthed) {
						App.ApplicationController.user = User.create({username:data.response.username});
						App.ApplicationController.authkey = data.response.authkey;
						getInfo(function (data) {
							App.ApplicationController.info = Info.create({messages:data.response.notifications.messages, notifications:data.response.notifications.notifications, subscriptions:data.response.notifications.subscriptions});
						});
						getNews(function (data) {
							App.ApplicationController.news = News.create({announcements:data.response.announcements, blogs:data.response.blogPosts});
						});
					} else {
						App.ApplicationController.user = null;
					}
					router.get('applicationController').connectOutlet('index');
					App.NavigationController.selected = 'index';
				});
			}
		}),
		login:Ember.Route.extend({
			route:'/login',
			connectOutlets:function (router) {
				router.get('applicationController').connectOutlet('loginForm');
			}
		}),
		logoout:Ember.Route.extend({
			route:'/logout',
			connectOutlets:function (router) {
				logout(App.ApplicationController.authkey, function () {
					router.transitionTo('root.index');
				});
			}
		}),
		inbox: Ember.Route.extend({
			route: '/inbox',
			connectOutlets: function(router) {
				inbox(function (data) {
					App.ApplicationController.hasMessages = data.response.pages > 0;
					App.ApplicationController.inbox = data.response;
					App.NavigationController.selected = 'inbox';
					router.get('applicationController').connectOutlet('inbox');
				});
			}
		}),
		torrents: Ember.Route.extend({
			route: '/torrents',
			connectOutlets: function(router) {
				torrents(function (data) {
					router.get('applicationController').connectOutlet('torrents');
					App.NavigationController.selected = 'torrents';
				});
			}
		}),
		forums: Ember.Route.extend({
			route: '/forums',
			connectOutlets: function(router) {
				torrents(function (data) {
					router.get('applicationController').connectOutlet('forums');
					App.NavigationController.selected = 'forums';
				});
			}
		}),
		subscriptions: Ember.Route.extend({
			route: '/subscriptions',
			connectOutlets: function(router) {
				torrents(function (data) {
					router.get('applicationController').connectOutlet('subscriptions');
					App.NavigationController.selected = 'subscriptions';
				});
			}
		}),
	})
});

App.initialize();

$('#login-form').live('submit', function (e) {
	var username = $('#login-username').val();
	var password = $('#login-password').val();
	console.log("abc");
	loginUser(username, password, function () {
		testLogin(function (isAuthed, data) {
			App.ApplicationController.loggedIn = isAuthed;
			App.ApplicationController.user = User.create({username:data.response.username});
			if (isAuthed) {
				console.log(data);
				// redirect to index
				App.get('router').transitionTo('root.index');
			} else {
				// do something to show invalid login and maybe number of attempts left
			}
		});
	});
	return false;
});
