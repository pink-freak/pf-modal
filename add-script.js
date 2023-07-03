/*追加で機能させるJS*/

// モーダル画面の領域外をクリックするとモーダルが閉じる
document.addEventListener('DOMContentLoaded', function() {
	var modals = document.querySelectorAll('.open-modal-content');
	
	modals.forEach(function(modal) {
	  var modalInner = modal.querySelector('.open-modal-content-inner');
	  
	  // 領域外クリックのイベントハンドラー
	  modal.addEventListener('click', function(event) {
		// イベントがモーダルの内部で発生したかどうかを確認します
		if (event.target !== modalInner && !modalInner.contains(event.target)) {
		  // モーダル外でのクリックの場合、チェックを外します
		  var parentModal = modal.parentElement; // このモーダルの親要素を取得
		  var modalCheck = parentModal.querySelector('input');
		  modalCheck.checked = false;
		}
	  });
	});
  });
  

  