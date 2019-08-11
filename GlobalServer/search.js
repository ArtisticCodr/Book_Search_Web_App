var search = angular.module('search', []);
search.controller('CtrlSearch', function($scope, $window, $compile, ServiceSearch) {
    var knjige = JSON.parse($window.localStorage.getItem("skriptKnjige"));
    console.log(knjige);
    if (knjige == null) {
        $window.location.href = "http://hadziserver.ddns.net:8080/globalserver/index.html";
    }
    if (knjige.length < 1) {
        var input = angular.element('<div>No result</div>');
        var compile = $compile(input)($scope);
        var element = angular.element(document.querySelector('.searchResult'));
        element.append(compile);
    }
    console.log('results: ' + knjige.length);
    for (i = 0; i < knjige.length; i++) {
        ServiceSearch.getKnjigaSmall(knjige[i].knjiga).then(function(response) {
            $scope.k = response.data;
            addKnjiga($scope, $compile);
        });
    }
    $scope.showBig = function() {
        if ($scope.big == true) {
            var element = angular.element(document.querySelector('.searchResult'));
            element.empty();
            for (i = 0; i < knjige.length; i++) {
                ServiceSearch.getKnjigaBig(knjige[i].knjiga).then(function(response) {
                    $scope.u = response.data;
                    addUnimarc($scope, $compile);
                });
            }
        } else {
            var element = angular.element(document.querySelector('.searchResult'));
            element.empty();
            for (i = 0; i < knjige.length; i++) {
                ServiceSearch.getKnjigaSmall(knjige[i].knjiga).then(function(response) {
                    $scope.k = response.data;
                    addKnjiga($scope, $compile);
                });
            }
        }
    }
});
search.factory('ServiceSearch', ['$http', function($http) {
    var service = {};
    service.getKnjigaSmall = function(br) {
        return $http.get('http://hadziserver.ddns.net:8080/globalserver/rest/knjigaSmall.php?knjiga=' + br);
    }
    service.getKnjigaBig = function(br) {
        return $http.get('http://hadziserver.ddns.net:8080/globalserver/rest/knjigaBig.php?knjiga=' + br);
    }
    return service;
}]);
var addKnjiga = function($scope, $compile) {
    var s = '<table id = "table" width="600px"> <tr id = "rows"> <th id = "rows" width="200x"> Naslov: </th> <td id = "rows"> ' + $scope.k.naslov + ' </td> </tr> <tr id = "rows"> <th id = "rows"> Autori: </th> <td id = "rows">';
    for (i = 0; i < $scope.k.autori.length; i++) {
        // posto u UNIMARK-u ime i prezime ne stoji na istom polju
        if (i < $scope.k.autori.length - 1) {
            s += $scope.k.autori[i + 1].autor + ' ' + $scope.k.autori[i].autor + '<br>';
            i++;
        } else {
            s += $scope.k.autori[i].autor + '<br>';
        }
    }
    s += '</td> </tr> <tr id = "rows"> <th id = "rows"> Godina Izdavanja: </th> <td id = "rows">' + $scope.k.godinaIzdavanja + '</td> </tr> <tr id = "rows"> <th id = "rows"> Mesto izdavanja: </th> <td id = "rows"> ' + $scope.k.mestoIzdavanja + ' </td> </tr> <tr id = "rows"> <th id = "rows"> Izdavac: </th> <td id = "rows"> ' + $scope.k.izdavac + ' </td> </tr> </table><br><br>';
    var input = angular.element(s);
    var compile = $compile(input)($scope);
    var element = angular.element(document.querySelector('.searchResult'));
    element.append(compile);
};
var addUnimarc = function($scope, $compile) {
    var s = '<table id = "table" width="800px"><tr id = "rows"><th id = "rows" width="600x">' + $scope.u.unimarc + '</th></tr></table><br><br>';
    var input = angular.element(s);
    var compile = $compile(input)($scope);
    var element = angular.element(document.querySelector('.searchResult'));
    element.append(compile);
};