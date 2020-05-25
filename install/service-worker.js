async function fetch_news(url) {
  let json;
  for (let i = 0; i < 5; i++) {  
    let response = await fetch(url+'&r='+i);
    if (response.status === 200) {
        json = await response.json();
        console.log(json);
		if (json.noads == 1 ) {
		return false;
		break;
		}		
        if (json !== null && (typeof json.title !== 'undefined')) {
            break;
        } 
    }
    console.log('wait a valid json '+(i+1)+' sec');
    await new Promise((resolve, reject) => setTimeout(resolve, 5000*(i+1)));
  }
  var jsonactions = json.actions;
  if (jsonactions) {
	jsonactions = JSON.parse(jsonactions);
	}
	
  
  let data = {link: json.link, requireInteraction: true, siteid: json.siteid, subid: json.subid, subs_id: json.subs_id  }
  fetch('https://SITE_URL/req.php?type=2&subs_id='+json.subs_id+'&sid='+json.siteid+'&advid='+json.advid+'&advsid='+json.advsid+'&sub='+json.subid, {method: 'PATCH', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({user_action: 'view'})})
  
  return  self.registration.showNotification(json.title, {
                body: json.body,
                icon: json.icon,
                image: json.image,
                actions: jsonactions,
                requireInteraction: true,
                tag: 'push',
                data: data
            });
}

self.addEventListener('push', function(event) {
    var json = event.data.json();
    console.log('data.json: '+json);
    if (json.data.subs_id && typeof json.data.subs_id !== 'undefined') {
        let rand_key = Math.floor(Math.random()*10000000);
        let subs_id = json.data.subs_id;
        var api_url = "https://SITE_URL/news.php?subs_id="+subs_id+'&rnd='+rand_key;
        console.log('API_URL:', api_url);
        const promiseChain = Promise.all([
            fetch_news(api_url),
            self.registration.update()
        ]);
        event.waitUntil(promiseChain);
    } else {
        var params = json.notification;
        params.image = json.data.image;
        if (json.data.actions) {
     	params.actions = JSON.parse(json.data.actions);
    	}
        params.data = {"link": params.click_action, "siteid": json.data.siteid, "subid": json.data.subid, "subs_id": json.data.subs_id2};
          const promiseChain = Promise.all([
        fetch('https://SITE_URL/req.php?type=2&subs_id='+json.data.subs_id2+'&sid='+json.data.siteid+'&advid='+json.data.advid+'&advsid='+json.data.advsid+'&sub='+json.data.subid, {method: 'PATCH', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({user_action: 'view'})}),
        self.registration.showNotification(params.title, params),
        self.registration.update()
       ]);
        event.waitUntil(promiseChain);
    }
    
});

self.addEventListener('install', function(event) {     
    console.log('install');
    fetch('https://SITE_URL/req.php?type=3'); 
});                                               
self.addEventListener('activate', function(event) {    
    console.log('activate');
    fetch('https://SITE_URL/req.php?type=4')                                                                                                                                  
});                                           
self.addEventListener('notificationclick', function(event) {
    const clickedNotification = event.notification;
	console.log('clickedNotification:', clickedNotification);
    event.notification.close();
    const promiseChain = Promise.all([fetch('https://SITE_URL/req.php?type=5&sid='+clickedNotification.data.siteid, {method: 'PATCH', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({user_action: 'click'})}) , clients.openWindow(clickedNotification.data.link)]);
    event.waitUntil(promiseChain);
}); 
self.addEventListener('notificationclose', function(event) {
	const notification = event.notification;
	console.log('notificationclose:', notification);
const promiseChain = Promise.all([fetch('https://SITE_URL/req.php?type=6&sid='+notification.data.siteid+'&sub='+notification.data.subid+'&subs_id='+notification.data.subs_id, {method: 'PATCH', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({user_action: 'close'})})]);
event.waitUntil(promiseChain);
});