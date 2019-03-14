crawlApp.factory("clipBoard", function($http,$q) {
    var copy_text = function (text)
    {
      var textArea = document.createElement("textarea");
      textArea.style.position = 'fixed';
      textArea.style.top = 0;
      textArea.style.left = 0;
      textArea.style.width = '2em';
      textArea.style.height = '2em';
      textArea.style.padding = 0;
      textArea.style.border = 'none';
      textArea.style.outline = 'none';
      textArea.style.boxShadow = 'none';
      textArea.style.background = 'transparent';
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();
      try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
      
      } catch (err) {
        console.log('Oops, unable to copy');
      }
      document.body.removeChild(textArea);
      if(msg=='successful')
        return true;
      else
        return false;
    }
return {
        copy_text:copy_text
    };

});
crawlApp.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });
 
                event.preventDefault();
            }
        });
    };
});

crawlApp.filter('countword', function () {
    return function (text) {
        if (text && (typeof text === 'string')) {
            var s = text ? text.split(/\s+/) : 0; // it splits the text on space/tab/enter
            return  s ? s.length : 0;
        } else {
            return 0;
        }
    };
});
// crawlApp.filter('countchar', function () {
//     return function (text) {
//         if (text && (typeof text === 'string')) {
//             var s = text ? text.replace(/ +/g, ' ').length : 0; // it splits the text on space/tab/enter
//             return  s;
//         } else {
//             return 0;
//         }
//     };
// });
crawlApp.directive('popover', function($compile, $timeout) {
   return {
    restrict: 'A',
    link:function(scope, el, attrs){
            var content = $("#popover-content").html();
            var compiledContent = $compile(content)(scope);
            var options = {
                content: compiledContent,
                html: true,
            };
            el.popover(options);
    }
  }
});
crawlApp.directive('jqdatepicker', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attrs, ctrl) {
            $(element).datepicker({
                dateFormat: 'yy-mm-dd',
                onSelect: function(date) {
                    ctrl.$setViewValue(date);
                    ctrl.$render();
                    scope.$apply();
                }
            });
        }
    };
});
/*crawlApp.directive('myBlock', function ($parse) {
    return {
        link: function (scope, elm, attrs) {
          
          elm.bind('keypress', function(e){
console.log(elm[0].value.length);
console.log(attrs);
            // stop typing if length is equal or greater then limit
            if(elm[0].value.length == attrs.max_len){
              e.preventDefault();
              return false;
            }
          });
        }
    }   
});*/
crawlApp.directive("limitToMax", function() {
  return {
    link: function(scope, element, attributes) {
      element.on("keydown keyup", function(e) {
        console.log(element.val());
    if (element.val().length = Number(attributes.max) && e.keyCode != 46  && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40)
       {
          e.preventDefault();
        }
      });
    }
  };
});

crawlApp.directive('myBlock', ['$parse', function ($parse) {
            return {
               link: function(scope, elm, attrs) {
                console.log('dddd');
                 elm.bind('keypress', function(e){
console.log(elm[0].value.length);
console.log($parse(attrs.max_len));
            // stop typing if length is equal or greater then limit
            if(elm[0].value.length == attrs.maxLen){
              e.preventDefault();
              return false;
            }
          });

               }
            };
         }]);


crawlApp.directive('fileModel', ['$parse', function ($parse) {
            return {
               restrict: 'A',
               link: function(scope, element, attrs) {
                  var model = $parse(attrs.fileModel);
                  var modelSetter = model.assign;
                  
                  element.bind('change', function(){
                     scope.$apply(function(){
                        modelSetter(scope, element[0].files[0]);
                     });
                  });
               }
            };
         }]);
crawlApp.directive('uppercased', function() {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, modelCtrl) {
            modelCtrl.$parsers.push(function(input) {
                return input ? input.toUpperCase() : "";
            });
            element.css("text-transform","uppercase");
        }
    };
})
crawlApp.directive('checklistModel', ['$parse', '$compile', function($parse, $compile) {
  // contains
  function contains(arr, item, comparator) {
    if (angular.isArray(arr)) {
      for (var i = arr.length; i--;) {
        if (comparator(arr[i], item)) {
          return true;
        }
      }
    }
    return false;
  }

  // add
  function add(arr, item, comparator) {
    arr = angular.isArray(arr) ? arr : [];
      if(!contains(arr, item, comparator)) {
          arr.push(item);
      }
    return arr;
  }  

  // remove
  function remove(arr, item, comparator) {
    if (angular.isArray(arr)) {
      for (var i = arr.length; i--;) {
        if (comparator(arr[i], item)) {
          arr.splice(i, 1);
          break;
        }
      }
    }
    return arr;
  }

  // http://stackoverflow.com/a/19228302/1458162
  function postLinkFn(scope, elem, attrs) {
    // compile with `ng-model` pointing to `checked`
    $compile(elem)(scope);

    // getter / setter for original model
    var getter = $parse(attrs.checklistModel);
    var setter = getter.assign;
    var checklistChange = $parse(attrs.checklistChange);

    // value added to list
    var value = $parse(attrs.checklistValue)(scope.$parent);


  var comparator = angular.equals;

  if (attrs.hasOwnProperty('checklistComparator')){
    comparator = $parse(attrs.checklistComparator)(scope.$parent);
  }

    // watch UI checked change
    scope.$watch('checked', function(newValue, oldValue) {
      if (newValue === oldValue) { 
        return;
      } 
      var current = getter(scope.$parent);
      if (newValue === true) {
        setter(scope.$parent, add(current, value, comparator));
      } else {
        setter(scope.$parent, remove(current, value, comparator));
      }

      if (checklistChange) {
        checklistChange(scope);
      }
    });
    
    // declare one function to be used for both $watch functions
    function setChecked(newArr, oldArr) {
        scope.checked = contains(newArr, value, comparator);
    }

    // watch original model change
    // use the faster $watchCollection method if it's available
    if (angular.isFunction(scope.$parent.$watchCollection)) {
        scope.$parent.$watchCollection(attrs.checklistModel, setChecked);
    } else {
        scope.$parent.$watch(attrs.checklistModel, setChecked, true);
    }
  }

  return {
    restrict: 'A',
    priority: 1000,
    terminal: true,
    scope: true,
    compile: function(tElement, tAttrs) {
      if (tElement[0].tagName !== 'INPUT' || tAttrs.type !== 'checkbox') {
        throw 'checklist-model should be applied to `input[type="checkbox"]`.';
      }

      if (!tAttrs.checklistValue) {
        throw 'You should provide `checklist-value`.';
      }

      // exclude recursion
      tElement.removeAttr('checklist-model');
      
      // local scope var storing individual checkbox model
      tElement.attr('ng-model', 'checked');

      return postLinkFn;
    }
  };
}]);

