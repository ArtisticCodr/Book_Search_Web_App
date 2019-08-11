var session = angular.module('session', []);
session.controller('CtrlSession', function($scope, $window, $compile, ServiceSession) {
    $scope.br = 0;
    $scope.upiti = [];
    ServiceSession.getSessionUpitCounter().then(function(response) {
        if (response.data == "false") {
            $window.location.href = "http://hadziserver.ddns.net:8080/globalserver/index.html";
        } else {
            $scope.counter = parseInt(response.data);
            for (i = 0; i < $scope.counter; i++) {
                ServiceSession.getSessionUpit(i).then(function(response) {
                    $scope.upit = response.data;
                    if (JSON.stringify($scope.upit) != '""') {
                        $scope.upiti[$scope.br] = {};
                        $scope.upiti[$scope.br].upit = $scope.upit;
                        $scope.upiti[$scope.br].br = $scope.br;
                        $scope.upit = $scope.upiti[$scope.br];
                        addUpit($scope, $compile);
                        $scope.br++;
                    }
                });
            }
        }
    });
    $scope.search = function() {
        ServiceSession.search($scope.upiti[$scope.searchUpit].upit).then(function(response) {
            $window.localStorage.setItem("skriptKnjige", JSON.stringify(response.data));
            $window.location.href = "http://hadziserver.ddns.net:8080/globalserver/search.html";
        });
    }
});
session.factory('ServiceSession', ['$http', function($http) {
    var service = {};
    service.getSessionUpit = function(br) {
        return $http.get('http://hadziserver.ddns.net:8080/globalserver/rest/sessionUpit.php?number=' + br);
    }
    service.getSessionUpitCounter = function() {
        return $http.get('http://hadziserver.ddns.net:8080/globalserver/rest/sessionUpit.php');
    }
    service.search = function(parameter) {
        return $http.post('http://hadziserver.ddns.net:8080/globalserver/rest/search.php', parameter);
    }
    return service;
}]);
var addUpit = function($scope, $compile) {
    var s = '<br><table id = "table" width="800px"><tr id = "rows"><th id = "rows" width="800x">' + JSON.stringify($scope.upit.upit) + '</th><th><button ng-click="searchUpit=' + $scope.upit.br + ';search();">Search</button></th></tr></table><br>';
    var input = angular.element(s);
    var compile = $compile(input)($scope);
    var element = angular.element(document.querySelector('.upiti'));
    element.append(compile);
};