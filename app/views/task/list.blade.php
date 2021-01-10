<link rel='stylesheet' href='{{ asset('css/list.css') }}'>

@if ( $errors->any() )
        {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
{{{ Session::has('msg') ? Session::get('msg') : '' }}}

@foreach ( $taskNodes as $taskNode )
    {{-- 親ノードを持たない者のみルートに表示 --}}
    @if ( $taskNode['no_parent'] )
        {{-- 親タスクリストをツリーごとに新規作成 --}}
        <?php
            unset($parent_id_list);
            $parent_id_list = [];
        ?>
        <div>
            @include('task.tree', ['node' => $taskNode, 'parent_id_list' => $parent_id_list ] )
        </div>
    @endif
@endforeach