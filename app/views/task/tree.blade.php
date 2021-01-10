<details>
    <summary>#{{ $node['task']['task_id']  }}&nbsp;{{ $node['task']['title'] }}</summary>
    <div class='view-edit'>
        <details>
            <summary>@lang('taskoj.edit')</summary>
            @include('task.edit', [
                'task_id' => $node['task']['task_id'], 
                'team' => $node['teamInfo']['team'], 
                'user_rank' => $node['task']['user_rank'], 
                'team_info' => $node['teamInfo'],
                'record' => $node['task'],
            ])
        </details>
    </div>

    <div class='view-children'>
    {{-- $node['task']['task_id'] --`}}
    {{-- var_export($parent_id_list, true) --}}
    {{-- すでに自分がいたら循環参照になるので取りやめ --}}
    @if ( !in_array( $node['task']['task_id'], $parent_id_list ) )
        {{-- 子タスクがあればネスト --}}
        @foreach ( $node['children'] as $childNode )
            {{-- もし子タスクがいれば、親タスクリストに自分のタスクIDを追加 --}}
            <?php
                $parent_id_list[] = $node['task']['task_id'];
            ?>
            @include('task.tree', ['node' => $childNode, 'parent_id_list' => $parent_id_list] )
        @endforeach
    @endif
    </div>    
</details>
