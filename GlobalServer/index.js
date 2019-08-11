var index = angular.module('index', []);
index.controller('CtrlIndex', function($scope, $window, $compile, ServiceIndex) {
    $scope.operators = [];
    $scope.prefixi = [];
    $scope.labele = [];
    $scope.values = [];
    $scope.selectedLab = [];
    $scope.inputCounter = 0;
    $scope.resultCounter = 0;
    $scope.cookie = document.cookie.split("=")[1];
    console.log("cookie: ", $scope.cookie);
    if ($scope.cookie == null) {
        //location.reload();
    } else {
        $window.localStorage.removeItem("skriptKnjige");
    }
    ServiceIndex.getPrefixes().then(function(response) {
        for (i = 0; i < response.data.length; i++) {
            $scope.labele[i] = response.data[i].label;
            $scope.prefixi[i] = response.data[i].prefix;
        }
        addKriterium($scope, $compile);
        addOperator($scope, $compile);
        addKriterium($scope, $compile);
        $scope.hide = false;
    });
    $scope.add = function() {
        addOperator($scope, $compile);
        addKriterium($scope, $compile);
    }
    $scope.search = function() {
        console.log("search");
        ServiceIndex.search(getSearchQuery($scope)).then(function(response) {
            if ($scope.hasResult == true) {
                $window.localStorage.setItem("skriptKnjige", JSON.stringify(response.data));
                $window.location.href = "http://hadziserver.ddns.net:8080/globalserver/search.html";
            }
        });
    }
});
index.factory('ServiceIndex', ['$http', function($http) {
    var service = {};
    service.getPrefixes = function() {
        return $http.get('http://hadziserver.ddns.net:8080/globalserver/rest/prefixes.php');
    }
    service.search = function(parameter) {
        return $http.post('http://hadziserver.ddns.net:8080/globalserver/rest/search.php', parameter);
    }
    return service;
}]);
var addKriterium = function($scope, $compile) {
    var input = angular.element('<table align="center"><tr><td><select ng-model="selectedLab[' + $scope.inputCounter + ']"><option value="" disabled selected hidden>Search by...</option><option ng-repeat="x in labele">{{x}}</option></select></td><td><input type="text" ng-model="values[' + $scope.inputCounter + ']" placeholder="value"></td></tr></table>');
    var compile = $compile(input)($scope);
    var element = angular.element(document.querySelector('.upit'));
    element.append(input);
    $scope.inputCounter++;
};
var addOperator = function($scope, $compile) {
    var input = angular.element('<table align="center"><tr><td><select ng-model="operators[' + ($scope.inputCounter - 1) + ']"><option value="" disabled selected hidden>Operator</option><option value="and">AND</option><option value="or">OR</option></select></td></tr></table>');
    var compile = $compile(input)($scope);
    var element = angular.element(document.querySelector('.upit'));
    element.append(input);
};
var getPrefixFromLabel = function($scope, label) {
    var i;
    for (i = 0; i < $scope.labele.length; i++) {
        if (label == $scope.labele[i]) {
            return $scope.prefixi[i];
        }
    }
}
var getSearchQuery = function($scope) {
    var i;
    var p = 0;
    var parameter = [];
    for (i = 0; i < $scope.inputCounter; i++) {
        parameter[p] = {};
        if ($scope.selectedLab[i] == null) {
            $scope.hasResult = false;
            return;
        }
        if ($scope.values[i] == null) {
            $scope.values[i] = "";
        }
        parameter[p].prefix = getPrefixFromLabel($scope, $scope.selectedLab[i]);
        parameter[p].value = $scope.values[i];
        p++;
        if (i < $scope.inputCounter - 1) {
            if ($scope.operators[i] == null) {
                $scope.hasResult = false;
                return;
            }
            parameter[p] = {};
            parameter[p].operator = $scope.operators[i];
            p++;
        }
    }
    $scope.hasResult = true;
    return parameter;
}