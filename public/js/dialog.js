/**
 * 確認ダイアログを表示し、OKを選択したときはリダイレクトする
 * 
 * @params url string リダイレクト先URL
 * @params msg string 警告メッセージ
 */
function confirmDialog( url, msg )
{
    if( window.confirm( msg ) )
    {
        console.log(url, "OK");
        window.location.href = url;
        return true;
    }
    else
    {
        console.log(url, "Cancel");
        return false;
    }
};
