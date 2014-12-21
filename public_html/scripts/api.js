api = {
	session: {
		postUser: function (data) {
			$.ajax({
				url: '/session-api/set_user_data',
				type: "POST",
				data: JSON.stringify(data),
				headers: {
					'Content-Type': 'application/json'
				},
				success: function (data) {
					console.log(data)
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error('textStatus',errorThrown)
				}
			});
		},
		getUser: function (storage) {
			$.ajax({
				url: '/session-api/get_user_data',
				type: "GET",
				success: function (data) {
					if (_.isObject(storage)) {
						storage.userData = JSON.parse(data);
					}
					console.log(storage )
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error('textStatus',errorThrown);
				}
			});
		}
	}
}