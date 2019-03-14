var crawlApp= angular.module('crawler',['ngFileUpload','ui.bootstrap']);
crawlApp.factory('myHttpInterceptor', function($q) {
  return {
    // optional method
    


    // optional method
    'response': function(response) {
    	if(response.data.status_code==303)
    		window.location = location.protocol+'//'+location.hostname;	
    	
      // do something on success
      return response;
    },

    // optional method
   'responseError': function(rejection) {
      // do something on error
      if (canRecover(rejection)) {
        return responseOrNewPromise
      }
      return $q.reject(rejection);
    }
  };
});
crawlApp.config(function ($httpProvider) {
    $httpProvider.defaults.transformRequest = function(data){
        if (data === undefined) {
            return data;
        }
        return $.param(data);
    };
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
    $httpProvider.interceptors.push('myHttpInterceptor');
});


// var stockApp= angular.module('stocking_app',['ngAnimate','ui.bootstrap','angularFileUpload']);
// stockApp.config(function ($httpProvider) {
//     $httpProvider.defaults.transformRequest = function(data){
//         if (data === undefined) {
//             return data;
//         }
//         return $.param(data);
//     };
//     $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
// });
// var HOST_NAME = 'http://'+location.hostname+'/master_pro/';
// var hname = location.hostname+'/master_pro/';
//stockApp.constant(‘hostName’,hname);
