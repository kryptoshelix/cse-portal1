@extends('admin.content._form')
@section('extraFields')
  <div class="col-md-4"><label for="project_type" class="form-label">Project type</label>
    <select id="project_type" name="project_type" class="form-select">
      @foreach(['student_project','faculty_research','capstone','rnd'] as $t)
        <option value="{{ $t }}" @selected(old('project_type',$item->project_type ?? 'student_project')===$t)>{{ str_replace('_',' ',ucfirst($t)) }}</option>
      @endforeach</select></div>
  <div class="col-md-4"><label for="academic_year" class="form-label">Academic year</label>
    <input id="academic_year" name="academic_year" placeholder="2025-26" class="form-control" value="{{ old('academic_year',$item->academic_year) }}"></div>
  <div class="col-md-4"><label for="technologies" class="form-label">Technologies</label>
    <input id="technologies" name="technologies" placeholder="React, FastAPI, PostgreSQL" class="form-control" value="{{ old('technologies',$item->technologies) }}"></div>
  <div class="col-md-6"><label for="guide" class="form-label">Guide / mentor</label>
    <input id="guide" name="guide" class="form-control" value="{{ old('guide',$item->guide) }}"></div>
  <div class="col-md-6"><label for="team_members" class="form-label">Team members</label>
    <input id="team_members" name="team_members" class="form-control" value="{{ old('team_members',$item->team_members) }}"></div>
  <div class="col-12"><label for="external_link" class="form-label">External link (repo / demo)</label>
    <input type="url" id="external_link" name="external_link" class="form-control @error('external_link') is-invalid @enderror" placeholder="https://" value="{{ old('external_link',$item->external_link) }}">
    @error('external_link')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endsection
