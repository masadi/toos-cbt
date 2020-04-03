<div class="collapse" id="collapseSoal">
    <div class="card card-body">
        <div class="row">
            <p>
                @if($keys)
                @foreach($keys as $question_id)
                <?php
            $jawaban = \App\User_question::where(function($query) use ($question_id, $user){
                $query->where('question_id', $question_id);
                if($user->peserta_didik_id){
                    $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
                } else {
                    $query->where('ptk_id', $user->ptk_id);
                }
            })->first();
            ?>
                <div class="col-3 mb-1">
                    @if($jawaban)
                    @if($jawaban->ragu)
                    <button
                        data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                        class="btn btn-block btn-navigasi btn-warning" type="button">{{$loop->iteration}}</button>
                    @else
                    @if($jawaban->answer_id)
                    <button
                        data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                        class="btn btn-block btn-navigasi btn-success" type="button">{{$loop->iteration}}</button>
                    @else
                    <button
                        data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                        class="btn btn-block btn-navigasi btn-secondary" type="button">{{$loop->iteration}}</button>
                    @endif
                    @endif
                    @else
                    <button
                        data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                        class="btn btn-block btn-navigasi btn-secondary" type="button">{{$loop->iteration}}</button>
                    @endif
                </div>
                <!--/loop-->
                @endforeach
                @else
                -
                @endif
            </p>
        </div>
    </div>
</div>