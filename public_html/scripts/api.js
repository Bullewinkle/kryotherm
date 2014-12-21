api = {
	session: {
		setUser: function (data, successCallback) {
			successCallback = successCallback || function (data) {
				console.log(data)
			};
			errorCallback = function (jqXHR, textStatus, errorThrown) {
				console.error('textStatus',errorThrown)
			}
			$.ajax({
				url: 'api/session/set_user_data',
				type: "POST",
				data: JSON.stringify(data),
				headers: {
					'Content-Type': 'application/json'
				},
				success: function (data) {
					data = JSON.parse(data);
					successCallback(data);
				},
				error: errorCallback
			});
		},
		getUser: function (storage, successCallback) {
			successCallback = successCallback || function (data) {
				console.log(data)
			};
			errorCallback = function (jqXHR, textStatus, errorThrown) {
				console.error('textStatus',errorThrown)
			}
			$.ajax({
				url: 'api/session/get_user_data',
				type: "GET",
				success: function (data) {
					data = JSON.parse(data);
					if (_.isObject(storage)) {
						storage.userData = data;
						successCallback(data)
					} else {
						successCallback(data)
					}
				},
				error: errorCallback
			});
		}
	}
}