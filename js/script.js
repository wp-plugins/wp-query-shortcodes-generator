/**
* wcpApp Module
*
* Description
*/

angular.module('wcpApp', []).controller('queryGenerator', ['$scope', function($scope){
	
	$scope.showShortcode = false;

	$scope.queryshortcode = {
		"qty": 1,
		"singleType": "post",
		"multipleType": "category",
		"tagid": "1",
		"numberofposts": 10,
		"cols": 2,
		"pageid": "",
		"postid": "",
		"productid": "1",
		"categoryid": "1"
	};

	$scope.queryshortcodeOptions = {
		"tags": true,
		"author": true,
		"date": true,
		"image": true,
		"pagination": true,
		"titlehr": true		
	};

	$scope.createShortcode = function(){

		if($scope.queryshortcode.qty == 1 && $scope.queryshortcode.singleType == 'post'){
			$scope.finalShortcode = '[wp-query p='+$scope.queryshortcode.postid+' tags='+$scope.queryshortcodeOptions.tags+' showauthor='+$scope.queryshortcodeOptions.author+' showdate='+$scope.queryshortcodeOptions.date+' showthumbnail='+$scope.queryshortcodeOptions.image+' showpagi='+$scope.queryshortcodeOptions.pagination+' hr='+$scope.queryshortcodeOptions.titlehr+']';	
		}

		else if($scope.queryshortcode.qty == 1 && $scope.queryshortcode.singleType == 'page'){
			$scope.finalShortcode = '[wp-query page_id='+$scope.queryshortcode.pageid+' tags='+$scope.queryshortcodeOptions.tags+' showauthor='+$scope.queryshortcodeOptions.author+' showdate='+$scope.queryshortcodeOptions.date+' showthumbnail='+$scope.queryshortcodeOptions.image+' showpagi='+$scope.queryshortcodeOptions.pagination+' hr='+$scope.queryshortcodeOptions.titlehr+']';		
		}

		else if($scope.queryshortcode.qty == 1 && $scope.queryshortcode.singleType == 'product'){
			$scope.finalShortcode = '[wp-query post_type=product p='+$scope.queryshortcode.productid+' tags='+$scope.queryshortcodeOptions.tags+' showauthor='+$scope.queryshortcodeOptions.author+' showdate='+$scope.queryshortcodeOptions.date+' showthumbnail='+$scope.queryshortcodeOptions.image+' showpagi='+$scope.queryshortcodeOptions.pagination+' hr='+$scope.queryshortcodeOptions.titlehr+']';	
		}

		else if($scope.queryshortcode.qty == 2 && $scope.queryshortcode.multipleType == 'category'){
			$scope.finalShortcode = '[wp-query cat='+$scope.queryshortcode.categoryid+' col='+$scope.queryshortcode.cols+' posts_per_page='+$scope.queryshortcode.numberofposts+' tags='+$scope.queryshortcodeOptions.tags+' showauthor='+$scope.queryshortcodeOptions.author+' showdate='+$scope.queryshortcodeOptions.date+' showthumbnail='+$scope.queryshortcodeOptions.image+' showpagi='+$scope.queryshortcodeOptions.pagination+' hr='+$scope.queryshortcodeOptions.titlehr+']';	
		}

		else if($scope.queryshortcode.qty == 2 && $scope.queryshortcode.multipleType == 'tag'){
			$scope.finalShortcode = '[wp-query tag_id='+$scope.queryshortcode.tagid+' col='+$scope.queryshortcode.cols+' posts_per_page='+$scope.queryshortcode.numberofposts+' tags='+$scope.queryshortcodeOptions.tags+' showauthor='+$scope.queryshortcodeOptions.author+' showdate='+$scope.queryshortcodeOptions.date+' showthumbnail='+$scope.queryshortcodeOptions.image+' showpagi='+$scope.queryshortcodeOptions.pagination+' hr='+$scope.queryshortcodeOptions.titlehr+']';	
		}
		else {
			alert('Please select options!');
		}

		$scope.showShortcode = true;
	}
}])