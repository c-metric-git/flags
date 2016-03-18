function addAnswer(message, url, form_key) {
	var nickname = $F('answer_nickname');
	var content = $F('answer_content');
	if(nickname && content) {
		new Ajax.Request(url, {
				method: 'post',
				parameters: {
					q_id: $F('id'),
					status: $F('answer_status'),
					nickname: nickname,
					content: content,
					form_key: form_key
				},
				onComplete: function(){
					window.location.reload();
				}
			}
		);
	} else {
		alert(message);
	}
}