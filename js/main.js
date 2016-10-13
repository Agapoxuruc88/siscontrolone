var miApp = angular.module('proyecto', ['ngRoute']);

miApp.controller('home', function($scope, $http){
	
	$scope.lstDatos = [];
	($scope.fnCargar = function(){
		$http.post('consultas.php', {accion: 'inicio'})
		.success(function( data ){
			console.log( data );
			$scope.lstDatos = data;
		}).error(function(data){
			console.log(data);
		});
	})();

	
});